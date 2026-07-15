# BE-05 — Resource Layer

## Status

Draft context document for the Resource layer in the WebSolutions backend.

This document focuses on `BlockCategoryResource` and related Resources in the current category endpoint path.

## Layer purpose

The Resource layer is the API serialization layer.

It is responsible for:

```text
- converting already-prepared Eloquent models into JSON-compatible arrays
- exposing frontend-oriented response shape
- using EavContentResolver on already-loaded collections
- using BlockAttachMap to route loaded block output
- preserving API contracts
```

It is not responsible for:

```text
- SQL queries
- Model::where calls
- Repository calls
- eager loading
- locale filtering in the database
- deciding which relations should exist
- compensating for incomplete repository loading
```

## Boundary rule

Primary rule:

```text
Resource serializes.
Repository prepares.
```

If a Resource needs additional data, the correct first move is to improve the Repository-prepared model graph, not to query from the Resource.

## Current target Resource

File:

```text
app/Http/Resources/BlockCategoryResource.php
```

Current helper methods:

```php
resolveContent(): array
resolveSections(): array
resolveSubitems($locale): array
toArray(Request $request): array
```

## Current good parts

### `resolveContent()`

Current behavior:

```text
- checks relationLoaded('blocks')
- finds block attached as content through BlockAttachMap
- checks block items loaded
- resolves first item through EavContentResolver
```

This is broadly aligned with the Resource role because it works with already-loaded relations.

### `resolveSections()`

Current behavior:

```text
- checks relationLoaded('blocks')
- iterates loaded blocks
- filters blocks attached to sections via BlockAttachMap
- resolves items through EavContentResolver
```

This is mostly serialization/mapping work.

Known caveat:

```text
Resource currently sorts sections by priority internally.
This should eventually move toward consistent sort policy, but it is not the first surgical target.
```

## Current problematic part

### `resolveSubitems($locale)`

Current problem:

```text
resolveSubitems() performs SQL/database queries from inside the Resource.
```

Problematic behavior:

```text
foreach child category:
  BlocksCategories::where('key', ...)->firstOrFail()
  BlocksCategories::with([...])->where('id', ...)->first()
```

This violates the Resource boundary.

The method currently acts as:

```text
serializer + repository + local data assembler
```

Target role:

```text
serializer only
```

## Current public response shape

For the control endpoint:

```text
GET /en/blocks/categories/services
```

The response shape must be preserved in the first pass.

Current root keys:

```text
id
key
name
description
content
parent_id
created_at
updated_at
section
sections
subcategories
blocks
```

Current important subcategory keys:

```text
id
slug
childs
title
descr
content
metadata
priority
```

First-pass policy:

```text
Do not rename subcategories.
Do not rename childs.
Do not remove children if present.
Do not change content object shape.
Do not change blocks output shape.
```

## `toArray()` concerns

Current `toArray()` uses:

```php
array_merge(
    $this->attributesToArray(),
    [...]
)
```

Risk:

```text
attributesToArray() exposes model columns implicitly.
Future DB columns can accidentally become public API.
```

Target direction:

```text
Use explicit public field list.
```

But first-pass policy:

```text
Do not change this unless the task explicitly includes public field contract hardening.
```

Reason:

```text
Changing attributesToArray() may affect the public shape.
The first surgical pass should focus on removing SQL from Resource while preserving output.
```

## `section` naming concern

Current Resource adds:

```php
'section' => $request->locale
```

Known debt:

```text
section currently equals locale in API output.
Frontend architecture increasingly treats URL context as scope, not simply locale.
```

First-pass policy:

```text
Do not rename section.
Do not change locale/scope semantics in the first Resource boundary refactor.
```

## `subcategories`, `children`, `childs`

Current meanings for first-pass work:

```text
subcategories
  public list of direct child categories enriched with EAV content; required by Services page.

childs
  legacy public key inside each subcategory object; currently empty array; preserve.

children
  recursive Resource output based on childrenRecursive; unresolved/legacy structural field; do not remove without separate audit.
```

Important:

```text
The first pass should not resolve naming debt.
It should preserve compatibility.
```

## Desired future `resolveSubitems()` behavior

After the minimal Repository fix, `resolveSubitems()` should behave like this conceptually:

```php
private function resolveSubitems(): array
{
    if (! $this->relationLoaded('children')) {
        return [];
    }

    return $this->children
        ->map(function ($category) {
            return array_merge(
                [
                    'id' => $category->id,
                    'slug' => $category->key,
                    'childs' => [],
                ],
                EavContentResolver::resolve($category->items, single: true)
            );
        })
        ->sortBy(...priority...)
        ->values()
        ->all();
}
```

This snippet is illustrative. The exact implementation must match project relations and output compatibility.

Critical point:

```text
No BlocksCategories::where() inside Resource.
No BlocksCategories::with() inside Resource.
```

## Related Resources

### `BlockResource`

Current role:

```text
- serializes block attributes
- attaches content through BlockAttachMap
- calls EavContentResolver for block items
- returns properties and items collections
```

Known issue:

```text
Also uses attributesToArray(). Candidate for future explicit contract pass.
```

First-pass policy:

```text
Do not refactor BlockResource in TASK-BE-001 unless absolutely required to preserve category output.
```

### `BlockItemResource`

Current role:

```text
- serializes item attributes
- converts propertyValues into properties object
```

Known issue:

```text
Duplicates some EAV flattening logic instead of using EavContentResolver consistently.
```

First-pass policy:

```text
Do not normalize BlockItemResource in the first category Resource boundary pass.
```

## Use of `EavContentResolver`

Allowed in Resource:

```text
EavContentResolver::resolve($alreadyLoadedItems, ...)
```

Not allowed:

```text
Query items from Resource, then pass them to resolver.
```

Rule:

```text
Resolver transforms loaded collections.
It does not excuse SQL inside Resource.
```

## Use of `BlockAttachMap`

Allowed in Resource:

```text
BlockAttachMap::is(...)
BlockAttachMap::isSingle(...)
BlockAttachMap::isKeyed(...)
BlockAttachMap::attach(...)
```

Current status:

```text
Temporary policy object.
Do not migrate to database fields in the first pass.
```

## First-pass Resource guardrails

For the accepted surgical refactor:

```text
Allowed:
- remove BlocksCategories import from BlockCategoryResource if no longer needed
- change resolveSubitems() to use loaded children/items
- keep existing output keys and sorting behavior
- add private pure serialization helper methods if useful

Not allowed:
- change routes
- change DB schema
- change public response keys
- delete subcategories / children / childs
- rewrite BlockAttachMap
- rewrite all Resources
- introduce broad Service layer
- move frontend contract names casually
```

## Manual regression checklist

After future code changes, compare before/after output for:

```text
GET /en/blocks/categories/services
```

Check:

```text
Root keys preserved.
content.title preserved.
content.descr preserved.
content.content preserved.
content.metadata preserved.
subcategories count preserved.
subcategories[].id preserved.
subcategories[].slug preserved.
subcategories[].childs preserved.
subcategories[].title preserved.
subcategories[].descr preserved.
subcategories[].content preserved.
subcategories[].metadata preserved.
subcategories[].priority preserved.
blocks[] shape remains compatible.
```

Code-level check:

```text
BlockCategoryResource must not import App\Models\BlocksCategories.
BlockCategoryResource must not call BlocksCategories::where().
BlockCategoryResource must not call BlocksCategories::with().
```

## Resource layer conclusion

The Resource layer should remain the public API presentation layer.

For the current backend focus:

```text
BlockCategoryResource is not to be made more intelligent.
It should become less responsible by receiving a better prepared category model from BlockCategoryRepository.
```
