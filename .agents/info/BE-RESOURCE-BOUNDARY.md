# BE-RESOURCE-BOUNDARY.md

## Purpose

This document defines the backend boundary between Repository, Resource, EAV resolver, and attach policy for the WebSolutions Laravel API.

It is written for the current refactoring direction:

```text
Surgical Variant A:
  keep the public endpoint shape,
  move data loading/preparation out of BlockCategoryResource,
  let Resource serialize already-prepared models.
```

This is not a task file and not an agent skill yet. It is source documentation for later `.agents/skills/*` and implementation prompts.

---

## Core Rule

```text
Repository prepares.
Resource serializes.
EavContentResolver transforms EAV items.
BlockAttachMap routes block output.
```

Expanded:

```text
Controller
  receives request and delegates

Repository
  performs SQL, eager loading, locale filtering, relation preparation

Resource
  converts already-loaded models into the public JSON shape

EavContentResolver
  converts already-loaded EAV item collections into flat arrays/objects

BlockAttachMap
  defines where block content is attached in the response
```

---

## Request Lifecycle

Control chain for the current focus:

```text
GET /en/blocks/categories/services
  → BlockCategoryController::index($locale, $slug)
    → BlockCategoryRepository::getCategory($locale, $slug)
      → BlockCategoryResource::toArray($request)
        → resolveContent()
        → resolveSections()
        → resolveSubitems()
        → BlockResource::collection(...)
        → JSON response
```

The problem is not the chain itself. The problem is that `BlockCategoryResource::resolveSubitems()` currently performs database queries, which breaks the intended layer boundary.

---

## Controller Boundary

Controller responsibility:

```text
- Accept route parameters.
- Call repository/query layer.
- Return Resource or JSON response.
```

Controller must not:

```text
- Build EAV payload manually.
- Resolve block attach behavior.
- Contain category tree cleanup logic.
- Contain frontend response mapping.
```

Current acceptable pattern:

```php
public function index(string $locale, string $slug): BlockCategoryResource
{
    return new BlockCategoryResource(
        $this->repo->getCategory($locale, $slug)
    );
}
```

---

## Repository Boundary

Repository is the only layer that may perform data retrieval for the category endpoint.

Repository may:

```text
- Use Eloquent queries.
- Use `where`, `whereHas`, `with`, `load`, `loadMissing`.
- Apply locale filtering.
- Apply category filtering.
- Load blocks.
- Load block items.
- Load property values.
- Load property definitions.
- Load direct children.
- Load child category items needed by `subcategories`.
- Perform recursive tree cleanup if the endpoint requires it.
```

Repository should return a model graph that is complete enough for Resource serialization.

For the services category endpoint, Repository should prepare at least:

```text
root category
  blocks
    properties
    items filtered by category_id and locale
      propertyValues filtered by locale
        property
  children filtered by locale availability
    items filtered by child category_id and locale
      propertyValues filtered by locale
        property
```

First-pass target:

```text
Move the data needed by `subcategories` into the model graph prepared by BlockCategoryRepository.
```

---

## Resource Boundary

Resource responsibility:

```text
- Expose the public JSON shape.
- Read already-loaded relations.
- Call EavContentResolver on already-loaded item collections.
- Call BlockAttachMap for attach policy.
- Build compatibility keys required by frontend.
```

Resource must not:

```text
- Execute SQL.
- Call `Model::where()`.
- Call `firstOrFail()` to fetch additional models.
- Call Repository.
- Apply DB-level locale filtering.
- Decide what relations should be loaded.
- Hide missing Repository loading by performing fallback queries.
```

Known current violation:

```text
BlockCategoryResource::resolveSubitems($locale)
  currently imports App\Models\BlocksCategories
  and performs BlocksCategories::where(...) / BlocksCategories::with(...)
```

Future refactor target:

```text
BlockCategoryResource::resolveSubitems()
  should iterate over already-loaded children
  and already-loaded child items/propertyValues/property.
```

---

## Resource Allowed Patterns

Allowed:

```php
if (! $this->relationLoaded('blocks')) {
    return [];
}
```

Allowed:

```php
$descrBlock = $this->blocks->first(
    fn ($block) => BlockAttachMap::is($block->key, 'content')
);
```

Allowed:

```php
return EavContentResolver::resolve($descrBlock->items, single: true);
```

Allowed:

```php
'blocks' => BlockResource::collection($this->whenLoaded('blocks'))
```

Allowed with caution:

```php
$this->attributesToArray()
```

It is currently part of the existing shape, but it is not ideal for long-term API contracts because it can leak future columns. Do not remove it in the first surgical pass unless the response contract has been explicitly frozen with explicit fields.

---

## Resource Forbidden Patterns

Forbidden inside Resource:

```php
BlocksCategories::where(...)
```

Forbidden inside Resource:

```php
BlocksCategories::with([...])->first()
```

Forbidden inside Resource:

```php
SomeRepository::...
```

Forbidden inside Resource:

```php
$q->whereHas(...)
$q->where('locale', ...)
```

Forbidden as first-pass cleanup:

```text
- Renaming public response keys.
- Removing `subcategories`.
- Removing `children`.
- Renaming `childs` to `children`.
- Changing BlockAttachMap behavior.
- Changing database schema.
```

---

## EavContentResolver Boundary

`EavContentResolver` is the transformation layer for EAV item collections.

It may:

```text
- Accept an already-loaded Collection of BlockItem models.
- Sort items if a sort/priority-like property is present according to current implementation.
- Flatten propertyValues into key-value objects.
- Decode JSON values.
- Cast integer, boolean, float, number values.
- Handle is_collection properties.
- Return singleton object, keyed object, or array.
```

It must not:

```text
- Query the database.
- Load missing relations.
- Know the current route.
- Know frontend component names.
- Decide whether an item belongs to a category.
- Decide locale filtering.
```

The resolver should be treated as a pure transformation utility for already-prepared EAV data.

---

## BlockAttachMap Boundary

`BlockAttachMap` is the current attach policy for block output.

It defines:

```text
- whether block content goes to `data.content`
- whether block content goes to `data.sections`
- whether block content remains in `data.blocks[]`
- whether content is singleton, array, or keyed
```

Current first-pass status:

```text
Frozen policy.
```

Meaning:

```text
Do not migrate attach behavior into the database in the first resource-boundary refactor.
Do not rename attach targets.
Do not change singleton/keyed behavior.
```

Long-term note:

```text
BlockAttachMap may later move toward database-backed metadata such as `blocks.attach`, `blocks.is_singleton`, and property-level metadata.
This is outside the current surgical pass.
```

---

## BlockResource Boundary

`BlockResource` currently serializes a block and includes:

```text
native block attributes
content resolved by EavContentResolver
attach from BlockAttachMap
properties collection
items collection
```

Current issue:

```text
BlockResource uses attributesToArray().
```

This is known technical debt but not the first target.

First-pass rule:

```text
Do not refactor BlockResource while fixing BlockCategoryResource unless strictly required to preserve the endpoint.
```

---

## BlockItemResource Boundary

`BlockItemResource` currently denormalizes `propertyValues` into `properties`.

Known future concern:

```text
BlockItemResource and EavContentResolver both perform EAV-to-properties style transformations.
They may diverge in type rules.
```

First-pass rule:

```text
Do not unify BlockItemResource and EavContentResolver in the category endpoint refactor.
Document it as future cleanup.
```

---

## Accepted First Refactor Direction

Accepted direction:

```text
Variant A — minimal and safest.
```

Target:

```text
BlockCategoryRepository::getCategory()
  prepares children/subcategory items fully enough
  for BlockCategoryResource to serialize without SQL.
```

Expected Resource behavior after refactor:

```text
resolveSubitems()
  checks relationLoaded('children')
  loops over loaded children
  uses loaded child items
  calls EavContentResolver::resolve(...)
  builds compatibility item shape
  performs no SQL
```

Expected Repository behavior after refactor:

```text
getCategory($locale, $key)
  loads root category blocks/items/propertyValues/property
  loads direct children that have localized content
  loads each child category's relevant items/propertyValues/property
```

---

## Manual Regression Boundary

Because the first pass should avoid unnecessary testing complexity, manual regression is acceptable.

Check before/after:

```text
GET /en/blocks/categories/services
```

Required comparison:

```text
- same envelope
- same root keys
- same `content` shape
- same `subcategories` key
- no unexpected subcategory count reduction
- same first-level subcategory keys
- same `blocks` key
- same `children` key
- no SQL/model query inside BlockCategoryResource
```

---

## Current Decision Record

Accepted:

```text
- Keep current API shape.
- Keep DB schema.
- Keep BlockAttachMap.
- Keep frontend untouched.
- Move toward Repository-prepared model graph.
- Treat Resource as serializer only.
```

Deferred:

```text
- Rename locale/section/scope.
- Rename childs.
- Merge children and subcategories.
- Redesign category endpoint shape.
- Move attach policy into DB.
- Introduce broad service layer.
- Unify all EAV property serializers.
- Replace all attributesToArray usages.
```

Core instruction for future tasks:

```text
Do not make BlockCategoryResource smarter.
Make its input model better prepared.
```
