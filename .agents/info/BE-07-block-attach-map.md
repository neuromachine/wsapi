# BE-07 — Block Attach Map

## Status

Architecture context / backend read-side documentation.

This document describes the current role of `App\Support\BlockAttachMap` in the WebSolutions backend. It is not a task file and does not prescribe immediate code changes.

---

## Purpose

`BlockAttachMap` is the current compatibility policy that decides where a block appears in a category API response.

It answers three questions:

```text
1. Where should this block be attached?
2. Should this block be treated as single-item content?
3. Should this block be keyed by item key?
```

It does not transform EAV values by itself. That is the role of `EavContentResolver`.

---

## Current Location

```text
app/Support/BlockAttachMap.php
```

Current public methods:

```php
BlockAttachMap::get(string $blockKey): ?string
BlockAttachMap::is(string $blockKey, string $attach): bool
BlockAttachMap::isSingle(string $blockKey): bool
BlockAttachMap::isKeyed(string $blockKey): bool
```

---

## Current Map

Current hardcoded policy:

```php
private const MAP = [
    'descr_data' => ['attach' => 'content',  'single' => true,  'keyed' => false],
    'slide'      => ['attach' => 'sections', 'single' => false, 'keyed' => true],
    'list'       => ['attach' => 'sections', 'single' => false, 'keyed' => true],
    'simplehtml' => ['attach' => 'sections', 'single' => true,  'keyed' => false],
    'works'      => ['attach' => 'sections', 'single' => false, 'keyed' => true],
];
```

This should be understood as current API compatibility behavior, not as final domain truth.

---

## Role in Category Response Assembly

A category endpoint may contain several loaded blocks.

Each block has a key:

```text
descr_data
slide
list
simplehtml
works
unknown block key
```

`BlockAttachMap` tells the Resource / response assembly layer how to route each block.

Conceptual flow:

```text
category.blocks[]
  → block.key
    → BlockAttachMap::get(block.key)
      → content / sections / null
        → EavContentResolver::resolve(block.items, single/keyed flags)
          → response JSON
```

---

## Attach Targets

### `content`

Used for the main descriptive content of a category/page.

Current known block:

```text
descr_data
```

Typical output:

```json
{
  "content": {
    "title": "Services",
    "descr": "...",
    "content": "<div>...</div>",
    "metadata": { ... },
    "priority": "2"
  }
}
```

In the `/en/blocks/categories/services` endpoint, this field is part of the public response contract.

---

### `sections`

Used for named page sections derived from specific block keys.

Current known blocks:

```text
slide
list
simplehtml
works
```

Typical output idea:

```json
{
  "sections": {
    "works": { ... },
    "slide": { ... }
  }
}
```

Exact shape depends on the block and `single/keyed` flags.

---

### `null` / unknown

If a block key is not mapped, it should not be forced into `content` or `sections` by guessing.

Current architecture often treats unknown blocks as generic `blocks[]` output.

This preserves data without inventing a semantic destination.

---

## Single and Keyed Flags

`BlockAttachMap` also controls how `EavContentResolver` is called.

### `single`

If true:

```php
EavContentResolver::resolve($items, single: true)
```

The first item becomes one flat object.

Used for singleton content such as `descr_data` and `simplehtml`.

### `keyed`

If true:

```php
EavContentResolver::resolve($items, single: false, keyed: true)
```

Items become an object keyed by `item.key`.

Used when item keys are meaningful frontend identifiers.

---

## What BlockAttachMap Should Do

Allowed responsibilities:

```text
- define current block-to-response placement policy
- expose attach target by block key
- expose single/keyed flags
- keep compatibility behavior centralized
```

---

## What BlockAttachMap Should Not Do

`BlockAttachMap` should not:

```text
- execute SQL
- know about locale filtering
- flatten EAV values
- call EavContentResolver itself
- know about frontend components
- become a full page composer
- decide category tree structure
```

If it starts needing these responsibilities, another layer is missing.

---

## Why It Is Hardcoded Now

The current map is a pragmatic compatibility layer.

It keeps response routing stable while the system is still evolving.

Hardcoding is not ideal, but it has advantages during this phase:

```text
- behavior is explicit
- no DB migration is required
- public API shape is stable
- agents can see all routing policy in one file
- refactors can preserve behavior more easily
```

For the current architecture work, this is acceptable as long as it is treated as transitional policy.

---

## Future Direction

Future architecture may move this policy into database metadata.

Possible future fields:

```text
blocks.attach
blocks.is_singleton
blocks.is_keyed
block_item_properties.meta.is_sort
```

Possible future model:

```text
Block schema/config
  → tells API where block belongs
  → tells resolver how to structure items
  → tells repository/order layer how to sort
```

This would make the backend more Server-Driven-UI friendly.

However, this is not a first-order refactor.

---

## Why Not Migrate It Now

Moving attach policy from code to DB would require:

```text
- DB schema change
- migration
- seed updates
- backfill of existing block metadata
- contract regression
- frontend verification
```

This is too risky to mix into read-side cleanup unless the task explicitly focuses on it.

Current guidance:

```text
Analyze BlockAttachMap.
Document BlockAttachMap.
Preserve BlockAttachMap during ordinary read-side refactors.
Migrate it only in a separate approved architecture task.
```

---

## Relationship to EavContentResolver

`BlockAttachMap` and `EavContentResolver` are complementary but separate.

```text
BlockAttachMap:
  where and how to attach block output

EavContentResolver:
  how to flatten loaded EAV values
```

Do not merge them.

Bad direction:

```text
BlockAttachMap starts flattening EAV
```

Bad direction:

```text
EavContentResolver starts deciding sections/content routing
```

Good direction:

```text
Response assembly layer calls BlockAttachMap for policy
and EavContentResolver for transformation.
```

---

## Relationship to Resource Layer

Current Resources may call `BlockAttachMap` to decide output placement.

This is acceptable if the Resource remains mostly a serializer.

But if the Resource begins to contain complex policy logic around multiple blocks, fallback structures, child categories, and nested assembly, this may indicate a need for an assembler/read-model builder.

`BlockAttachMap` does not solve that by itself. It only centralizes one specific policy.

---

## Relationship to Architecture Improvement Proposals

Future AIP documents should revisit BlockAttachMap when discussing:

```text
- Server-Driven UI
- API-driven page configuration
- block metadata
- category response contract evolution
- migration from hardcoded routing to DB-backed config
```

Until then, keep the current map stable.

---

## Known Debt

Known debt around this area:

```text
- attach policy is hardcoded
- unknown block behavior is implicit in Resource logic
- single/keyed flags are hidden code policy
- sort behavior is not represented here
- block metadata is not yet stored in DB
```

This debt is real, but not urgent enough to combine with every refactor.

---

## Guidance for Future Agents

When working near block response assembly:

```text
1. Inspect BlockAttachMap before changing response routing.
2. Do not invent new attach targets without contract review.
3. Do not move attach policy to DB unless task explicitly asks.
4. Preserve content / sections / blocks behavior.
5. Preserve single/keyed behavior unless regression proves safe.
6. Treat unknown block keys conservatively.
7. Do not merge attach policy with EAV flattening.
```

---

## Summary

`BlockAttachMap` is the current compatibility policy for block placement in API responses.

Its correct role:

```text
block key
  → attach target + single/keyed flags
```

It should remain small, explicit, and stable until a dedicated architecture improvement task replaces it with DB-backed metadata.
