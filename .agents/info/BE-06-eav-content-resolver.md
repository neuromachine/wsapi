# BE-06 — EAV Content Resolver

## Status

Architecture context / backend read-side documentation.

This document describes the role of `App\Support\EavContentResolver` in the WebSolutions backend. It is not a task file and does not prescribe immediate code changes.

---

## Purpose

`EavContentResolver` is the current transformation boundary between the internal EAV storage model and the frontend-facing JSON payload.

Its core responsibility:

```text
BlockItem collection with propertyValues
    ↓
flat associative array / array of arrays / keyed object
```

It exists because the database stores dynamic content through:

```text
block_items
  → block_item_property_values
      → block_item_properties
```

but the frontend should receive logical objects such as:

```json
{
  "title": "Services",
  "descr": "...",
  "content": "...",
  "metadata": { ... }
}
```

The frontend must not know about EAV internals such as `property_id`, `item_id`, `propertyValues`, `value_type`, or `is_collection`.

---

## Current Location

```text
app/Support/EavContentResolver.php
```

Current public method:

```php
public static function resolve(
    Collection $items,
    bool $single = true,
    bool $keyed = false
): array
```

---

## Current Role in the Read-Side Flow

In the current backend flow, `EavContentResolver` is used by Resources to transform already available EAV item collections.

Typical flow:

```text
Repository
  → loads category / blocks / items / propertyValues / property
    → Resource receives prepared model graph
      → Resource calls EavContentResolver::resolve(...)
        → JSON payload contains flat frontend-facing objects
```

Important rule:

```text
EavContentResolver must transform loaded data.
It must not load data.
```

---

## Input Expectations

`resolve()` expects a Laravel `Collection` of `BlockItem` models.

Each item is expected to have:

```text
item.propertyValues
item.propertyValues[].property
```

already loaded.

This means the Repository / query layer should prepare:

```php
'items.propertyValues.property'
```

or equivalent nested eager loading before the Resource calls the resolver.

If these relations are not loaded, the resolver may trigger lazy loading indirectly through model property access. That would be undesirable in a stricter architecture.

Future improvement may include explicit protection against unloaded relations, but that is not part of the current documentation task.

---

## Output Modes

The resolver supports three practical output modes.

---

### 1. Single mode

Call:

```php
EavContentResolver::resolve($items, single: true)
```

Behavior:

```text
items.first()
  → flattenItem(first item)
```

Used for singleton-like blocks or category descriptive data.

Example logical output:

```json
{
  "title": "Services",
  "descr": "We build data-driven marketing...",
  "content": "<div>...</div>",
  "metadata": { ... },
  "priority": "2"
}
```

Current use cases include structures such as:

```text
descr_data
category descriptive content
single page/content item
```

---

### 2. Keyed mode

Call:

```php
EavContentResolver::resolve($items, single: false, keyed: true)
```

Behavior:

```text
items.mapWithKeys(item.key => flattenItem(item))
```

Used when the frontend needs an object indexed by item key.

Example:

```json
{
  "shincenter": {
    "title": "Shincenter",
    "descr": "..."
  },
  "rayaray": {
    "title": "RAYA RAY",
    "descr": "..."
  }
}
```

This is useful for sections where the item key acts as a stable logical identifier.

---

### 3. Array mode

Call:

```php
EavContentResolver::resolve($items, single: false, keyed: false)
```

Behavior:

```text
items.map(flattenItem).values().all()
```

Used when the frontend expects a list.

Example:

```json
[
  {
    "title": "Item A",
    "descr": "..."
  },
  {
    "title": "Item B",
    "descr": "..."
  }
]
```

---

## Flattening Logic

The private `flattenItem()` method is the core mapping step.

For every property value:

```text
propertyValues[]
  → property.key
  → cast(value, value_type)
  → assign to result[property.key]
```

Conceptual example:

```text
PropertyValue(property.key = title, value = Services)
PropertyValue(property.key = descr, value = ...)
PropertyValue(property.key = metadata, value_type = json, value = {...})

↓

{
  title: "Services",
  descr: "...",
  metadata: {...}
}
```

The public API receives property keys, not database IDs.

---

## Type Casting

Current casting is based on `block_item_property_values.value_type`.

Supported behavior:

```php
match($type) {
    'json'    => json_decode($value, associative: true) ?? $value,
    'integer' => (int) $value,
    'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
    'float'   => (float) $value,
    'number'  => is_numeric($value) ? $value + 0 : $value,
    default   => $value,
}
```

Important distinction:

```text
property.type
value.value_type
```

The current resolver uses `value_type`, not `property.type`.

This is important because other parts of the code may still contain older or parallel transformation logic. Future consolidation should prefer one consistent rule, but such consolidation must be treated as a separate refactor.

---

## Collection Properties

Collection behavior is controlled by:

```text
property.is_collection
```

If `is_collection` is true:

```php
$result[$key][] = $value;
```

If false:

```php
$result[$key] = $value;
```

This allows a property such as `image` or `files` to become an array while ordinary properties stay scalar.

Example:

```json
{
  "image": ["a.png", "b.png"],
  "title": "Portfolio item"
}
```

---

## Sorting Behavior

Before resolving, the current implementation calls:

```php
sortIfNeeded($items)
```

The current rule:

```text
If the first item has a property with key = sort,
sort the whole collection by that value.
```

This is a pragmatic compatibility behavior.

Known limitations:

```text
- sort detection depends on the first item
- sort key is a content property, not structural metadata
- sorting is currently transformation-time behavior
```

Future architecture may move sorting toward:

```text
- Repository query ordering
- explicit metadata on block_item_properties.meta
- explicit block-level configuration
```

But this must not be mixed into unrelated refactors without regression checks.

---

## What the Resolver Should Do

Allowed responsibilities:

```text
- flatten EAV item values
- use property.key as output field name
- cast values by value_type
- respect property.is_collection
- return single / keyed / array structures
- keep frontend unaware of EAV internals
```

---

## What the Resolver Should Not Do

The resolver should not:

```text
- perform SQL queries
- call repositories
- decide which blocks belong to content / sections / blocks
- decide locale filtering
- decide category tree loading
- know about frontend routes
- mutate models
- become a general payload builder
```

If it starts needing those responsibilities, the missing layer is likely Repository preparation, an assembler, or a dedicated read-side builder — not a larger resolver.

---

## Relationship to Resource Layer

Resources may call `EavContentResolver` only on data they already received.

Acceptable:

```php
EavContentResolver::resolve($block->items, single: true)
```

when `$block->items.propertyValues.property` has already been prepared.

Risky:

```php
EavContentResolver::resolve($category->items, single: true)
```

if `items` or `propertyValues.property` were not eager-loaded.

Not acceptable:

```text
Resource notices missing data
  → Resource queries DB
  → Resource then calls resolver
```

That pattern is the core architecture smell currently being addressed in the broader backend refactor direction.

---

## Relationship to Repository Layer

Repository should ensure the resolver has enough data.

For a category endpoint, this often means preparing:

```php
'blocks.items.propertyValues.property'
'children.items.propertyValues.property'
```

or any equivalent graph required by the response contract.

The Repository decides what data exists in the graph. The resolver decides how loaded EAV values become plain arrays.

---

## Relationship to BlockAttachMap

`BlockAttachMap` decides where block output goes.

`EavContentResolver` decides how block item content is flattened.

These are separate concerns:

```text
BlockAttachMap:
  hero → sections.hero
  descr_data → content
  unknown → blocks[]

EavContentResolver:
  propertyValues → { title, descr, content, ... }
```

Do not merge these responsibilities.

---

## Known Debt

Current known debt around this area:

```text
- duplicated or parallel transformation logic may exist in Resource classes
- sort behavior is content-key based
- missing explicit relation-loaded checks may hide lazy loading
- no strict output schema validation
- type source should be clarified across all transformation paths
```

These are future refactor candidates.

---

## Guidance for Future Agents

When touching EAV response generation:

```text
1. Inspect the endpoint response contract first.
2. Do not expose EAV internals.
3. Do not move SQL into the resolver.
4. Do not rewrite casting rules without regression data.
5. Preserve single/keyed/array behavior unless task explicitly allows change.
6. Check that Repository prepares required relations.
7. Treat changes to sorting as contract-sensitive.
```

---

## Summary

`EavContentResolver` is a focused transformation utility.

Its correct role:

```text
loaded EAV item collection
  → flat frontend-facing object(s)
```

It is not a query layer, not a category assembler, and not a response router.

Keeping this boundary stable is essential for future backend read-side refactoring.
