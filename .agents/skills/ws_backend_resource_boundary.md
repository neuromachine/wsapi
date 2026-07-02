# Skill: WS Backend Resource Boundary

## Purpose

Use this skill when editing Laravel API Resources or code that prepares data for Resources.

The Resource layer must remain focused on serialization of already-prepared data.

Core rule:

```text
Repository prepares. Resource serializes.
```

---

## Required context

Read:

```text
.agents/info/BE-05-resource-layer.md
.agents/info/BE-RESOURCE-BOUNDARY.md
.agents/info/BE-02-blocks-read-side-flow.md
.agents/info/improvements/AIP-BE-003-resource-to-assembler-boundary.md
```

---

## Resource responsibilities

Resources may:

```text
- return explicit API arrays
- serialize already-loaded models and relations
- call pure transformation helpers on already-loaded data
- apply compatibility aliases
- perform simple null-safe formatting
- preserve current public response shape
```

Resources may call helpers such as `EavContentResolver` only when the helper receives already-loaded data and does not perform SQL.

---

## Resource anti-patterns

Resources must not:

```text
- perform SQL queries
- call Model::where(), Model::with(), first(), firstOrFail() for data loading
- call Repository classes
- decide which records should be loaded from the database
- perform locale filtering through queries
- hide missing eager loading by querying again
- become a large response orchestration service
```

A Resource that compensates for incomplete Repository loading is a boundary violation.

---

## Explicit mapping vs attributesToArray

Avoid using model-wide `attributesToArray()` as the public API contract when possible.

Preferred direction:

```php
return [
    'id' => $this->id,
    'key' => $this->key,
    'name' => $this->name,
];
```

Why:

```text
- future DB columns should not leak into API by accident
- API contract should be intentional
- compatibility is easier to reason about
```

If changing `attributesToArray()` is too broad for the current task, document it as remaining debt.

---

## Handling complex Resources

If a Resource becomes responsible for too much response assembly, consider whether a small read-side assembler is justified.

A Resource may be too smart when it:

```text
- coordinates multiple response sections
- applies nontrivial routing policy
- merges category structure and EAV content
- sorts multiple subtrees
- handles compatibility for several legacy shapes
- grows large private methods that are not simple serialization
```

Do not introduce an assembler automatically. First prove that it clarifies the system.

---

## Acceptable pattern

Good Resource pattern:

```text
- receives a prepared model/read structure
- checks relationLoaded when needed
- serializes fields explicitly
- delegates EAV flattening to EavContentResolver
- preserves public keys
- avoids DB access
```

Example direction:

```php
private function resolveSubcategories(): array
{
    if (! $this->relationLoaded('children')) {
        return [];
    }

    return $this->children->map(function ($category) {
        return [
            'id' => $category->id,
            'slug' => $category->key,
            'childs' => [],
            // merge already-loaded EAV data if present
        ];
    })->values()->all();
}
```

This is an example of direction, not a required exact implementation.

---

## Unacceptable pattern

Bad Resource pattern:

```php
$newCat = BlocksCategories::where('key', $category->key)->firstOrFail();

$subCat = BlocksCategories::with([...])
    ->where('id', $category->id)
    ->first();
```

This performs data loading inside serialization and should be refactored.

---

## Reporting requirements

When changing Resources, report:

```text
- whether any SQL/model querying remains inside the Resource
- what data the Resource now expects to be prepared before serialization
- what public keys were preserved
- whether `attributesToArray()` remains and why
- whether a future assembler may be appropriate
```

