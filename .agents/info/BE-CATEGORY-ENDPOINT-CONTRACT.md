# BE-CATEGORY-ENDPOINT-CONTRACT.md

## Purpose

This document freezes the current backend API contract for the category endpoint used by the WebSolutions services page.

It is not a refactoring task and not an implementation guide. Its role is to protect the existing public response shape while the backend is being prepared for a minimal, surgical refactor of the Repository → Resource boundary.

Primary control chain:

```text
Frontend route:
  /en/services

API request:
  GET /en/blocks/categories/services

Backend chain:
  routes/api.php
    → BlockCategoryController::index(string $locale, string $slug)
      → BlockCategoryRepository::getCategory($locale, $slug)
        → BlockCategoryResource
          → JSON Resource response
```

Current practical focus:

```text
Target endpoint:
  GET /en/blocks/categories/services

Target backend files:
  app/Repositories/BlockCategoryRepository.php
  app/Http/Resources/BlockCategoryResource.php
  app/Support/EavContentResolver.php
  app/Support/BlockAttachMap.php
  app/Http/Resources/BlockResource.php
  app/Http/Resources/BlockItemResource.php

Regression reference:
  services.json
```

---

## Current Public Response Shape

The current response follows the Laravel Resource envelope:

```json
{
  "data": {}
}
```

The inner `data` object for `/en/blocks/categories/services` currently contains at least:

```text
data.id
data.key
data.name
data.description
data.content
data.parent_id
data.created_at
data.updated_at
data.section
data.sections
data.subcategories
data.blocks
data.children
```

Do not remove or rename these keys in the first backend refactor pass.

---

## Root Category Contract

The root category object represents the requested category:

```text
key = services
section = en
```

Expected root-level fields:

```json
{
  "id": 2,
  "key": "services",
  "name": "...",
  "description": null,
  "content": {},
  "parent_id": null,
  "created_at": null,
  "updated_at": null,
  "section": "en",
  "sections": [],
  "subcategories": [],
  "blocks": [],
  "children": []
}
```

Notes:

```text
- `section` is currently derived from the route locale parameter.
- The current backend name may contain encoding artifacts in exported data. Do not attempt to fix encoding in the resource-boundary refactor.
- `description`, `created_at`, and `updated_at` are part of the current exposed shape because the resource currently merges native model attributes.
```

---

## `data.content`

`data.content` is the primary content object for the requested category.

It is currently produced by `BlockCategoryResource::resolveContent()` by selecting the block whose `BlockAttachMap` target is `content`, normally `descr_data`, and resolving its items through `EavContentResolver` in singleton mode.

Expected shape:

```json
{
  "title": "Services",
  "descr": "We build data-driven marketing where every decision is based on data, not hypotheses",
  "content": "<...HTML...>",
  "metadata": {
    "og": {},
    "twitter": {},
    "seo": {}
  },
  "priority": "2"
}
```

Contract notes:

```text
- `content.content` may contain trusted HTML from the internal content pipeline.
- `metadata` is a decoded JSON object.
- `priority` currently may be a string in the public response.
- Do not normalize types in the first resource-boundary refactor unless the current response already does so.
```

---

## `data.sections`

`data.sections` is reserved for blocks attached as named sections.

Current observed state for `/en/blocks/categories/services`:

```json
"sections": []
```

Contract rule:

```text
Keep the key present.
Do not remove it because it is part of the category response convention.
Do not redesign section routing in the first backend refactor pass.
```

---

## `data.subcategories`

`data.subcategories` is the important public collection for the services page.

It contains direct service directions under the root `services` category, with each child category enriched by EAV content from the `descr_data` block.

Expected item shape:

```json
{
  "id": 1024,
  "slug": "prodvizenie",
  "childs": [],
  "title": "Digital Marketing",
  "descr": "Comprehensive digital marketing to increase your business visibility online",
  "content": "<...HTML...>",
  "metadata": {
    "og": {},
    "twitter": {},
    "seo": {}
  },
  "priority": "..."
}
```

Required compatibility keys:

```text
subcategories[].id
subcategories[].slug
subcategories[].childs
subcategories[].title
subcategories[].descr
subcategories[].content
subcategories[].metadata
subcategories[].priority
```

Important terminology:

```text
subcategories = current public API contract for category page children enriched with content
children      = structural recursive category representation, currently less confirmed for /services
childs        = legacy spelling used in the current response and frontend consumers
```

First-pass rule:

```text
Do not remove `subcategories`.
Do not rename `childs` to `children`.
Do not merge `subcategories` and `children`.
Do not change ordering rules unless explicitly requested.
```

---

## `data.blocks`

`data.blocks` exposes loaded blocks for the requested category.

Observed block shape includes:

```text
blocks[].id
blocks[].key
blocks[].name
blocks[].description
blocks[].created_at
blocks[].updated_at
blocks[].laravel_through_key
blocks[].content
blocks[].attach
blocks[].properties
blocks[].items
```

For the services root category, `descr_data` appears as a block with:

```text
key = descr_data
attach = content
content = resolved singleton EAV object
properties = block property definitions
items = raw item resources with properties
```

First-pass rule:

```text
Do not redesign `blocks`.
Do not remove `properties` or `items` from `BlockResource` output in the category refactor.
Do not change `attach` values.
```

The `blocks` payload is verbose, but it is part of the current response. Any reduction should be a separate API-contract task.

---

## `data.children`

`data.children` currently represents structural recursive category children through `BlockCategoryResource::collection($this->whenLoaded('childrenRecursive'))`.

Current status:

```text
- Do not remove.
- Do not rely on it as the primary services-page collection until a separate audit confirms its real frontend consumers.
- Do not merge with `subcategories` in the first pass.
```

---

## Locale / Section Behavior

Current route parameter:

```text
{locale} = en
```

Current behavior:

```text
- Passed to BlockCategoryRepository::getCategory($locale, $slug).
- Used to filter `propertyValues.locale`.
- Passed to BlockCategoryResource through request context.
- Currently exposed as `data.section`.
```

Terminology note:

```text
Frontend architecture increasingly treats this as `scope`.
Backend currently calls it `locale` / `section` in places.
Do not rename this in the surgical resource-boundary refactor.
```

---

## Current Known Debt

The following issues are known but must not be fixed inside the first contract-preserving refactor unless explicitly requested:

```text
- `childs` spelling is legacy.
- `sections` may be an empty array instead of object for this endpoint.
- `blocks` exposes verbose internals like properties and items.
- `BlockResource` currently uses attributesToArray().
- `BlockCategoryResource` currently uses attributesToArray().
- Some exported strings contain encoding artifacts.
- `section` naming is semantically weaker than future `scope`.
```

---

## Regression Checklist

When refactoring this endpoint, compare the response before and after for:

```text
GET /en/blocks/categories/services
```

Minimum manual regression checks:

```text
[ ] Response still uses `data` envelope.
[ ] `data.key` is still `services`.
[ ] `data.section` is still `en`.
[ ] `data.content.title` exists.
[ ] `data.content.descr` exists.
[ ] `data.content.content` exists.
[ ] `data.content.metadata` exists.
[ ] `data.sections` key still exists.
[ ] `data.subcategories` key still exists.
[ ] `data.subcategories` count is not reduced unexpectedly.
[ ] First subcategory still has id, slug, childs, title, descr, content, metadata, priority.
[ ] `data.blocks` key still exists.
[ ] `data.blocks[].content` still exists.
[ ] `data.blocks[].attach` still exists.
[ ] `data.children` key still exists.
```

---

## First Refactor Boundary

Accepted surgical direction:

```text
Variant A — minimal and safest.
```

Goal of future implementation task:

```text
Preserve this endpoint contract while moving subcategory data preparation out of BlockCategoryResource and into Repository-level loading/preparation.
```

Non-goals:

```text
- No route changes.
- No DB schema changes.
- No JSON key renames.
- No `subcategories`/`children` redesign.
- No BlockAttachMap migration.
- No frontend changes.
- No broad service-layer rewrite.
```
