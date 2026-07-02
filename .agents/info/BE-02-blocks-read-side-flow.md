# BE-02 — Blocks Read-Side Flow

## Status

Draft architecture context.

## Purpose

This document describes how data is lifted from the database and returned to the Vue frontend through the Laravel Blocks read-side API.

It focuses on the practical flow around category endpoints, especially:

```text
GET /en/blocks/categories/services
```

The document also records why the current data lifting flow became difficult to maintain and how future refactoring should approach it.

This is not a task file. It is context for architectural understanding and future agentic refactoring.

---

## 1. What “read-side flow” means

In this project, read-side flow means the full path from an HTTP GET request to the final frontend-facing JSON payload.

For Blocks/Categories, the simplified flow is:

```text
HTTP request
  → API route
    → Controller
      → Repository / query layer
        → Eloquent model graph
          → Resource
            → Support transformers
              → JSON response
                → Vue frontend
```

The important point:

```text
The frontend does not consume the database model.
The frontend consumes a prepared JSON contract.
```

Therefore, the backend read-side is not just “fetch records”. It is a content delivery pipeline.

---

## 2. Control example: services page

Frontend route:

```text
/en/services
```

Backend request:

```text
GET /en/blocks/categories/services
```

Expected high-level payload:

```text
data
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
  children
```

The `services.json` payload captured from Postman is currently treated as a regression reference for this endpoint.

---

## 3. Current conceptual model

The backend data model is based on a block/content system with EAV-style dynamic properties.

Core tables:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

Conceptual entities:

```text
Block
  logical content/entity type

BlockItem
  concrete entity instance

BlockItemProperty
  schema/field descriptor

BlockItemPropertyValue
  localized typed value

BlocksCategories
  content/taxonomy tree node
```

The backend stores flexible EAV data but returns flattened frontend-oriented objects.

---

## 4. Main category endpoint flow

The expected responsibility split is:

```text
Route
  selects endpoint and route params

Controller
  receives locale and slug
  delegates data loading

Repository
  builds query
  applies locale filter
  eager-loads required relations
  returns prepared model graph

Resource
  serializes the prepared graph
  calls pure transformation helpers where needed
  preserves public JSON shape

EavContentResolver
  turns loaded EAV items into flat arrays

BlockAttachMap
  tells where block output belongs: content / sections / blocks
```

The intended architecture is clear. The main issue is that some current implementation details blur these boundaries.

---

## 5. The current “data lifting” problem

The phrase “усложненный / костыльный подъем данных” refers to cases where the backend does not have a clean, predictable flow for preparing the response.

Symptoms include:

```text
- Resource performs SQL queries because Repository did not prepare enough data
- Resource decides what related data should be loaded
- EAV transformation appears in multiple places
- response structure depends on hardcoded attach behavior
- public JSON keys mix legacy naming and current needs
- sorting and priority behavior are not clearly owned by one layer
- `attributesToArray()` risks exposing implicit model columns as public API
```

The known concrete issue:

```text
BlockCategoryResource::resolveSubitems()
  performed additional BlocksCategories queries
  while generating `subcategories`
```

This violates the desired boundary:

```text
Repository prepares.
Resource serializes.
```

---

## 6. Why a narrow patch was not enough

A previous narrow task focused on moving SQL out of `BlockCategoryResource::resolveSubitems()`.

The direction was correct, but the framing was too narrow.

The real problem is broader:

```text
The category endpoint is an assembled read model.
It is not just a category row plus relations.
```

The response includes multiple conceptual parts:

```text
content
  page-level content derived from a block/item mapping

sections
  block outputs routed into named sections

subcategories
  frontend-facing child category cards with EAV-derived fields

blocks
  remaining block payloads

children
  structural/recursive category data or legacy compatibility field
```

Therefore, future refactoring should analyze the full response assembly, not only one method.

---

## 7. Current public response zones

### 7.1 `content`

Represents main category/page content.

Typically derived from a block such as `descr_data` and resolved through EAV data.

Expected fields may include:

```text
title
descr
content
metadata
priority
```

### 7.2 `sections`

Represents blocks routed into named frontend sections.

Routing is currently governed by `BlockAttachMap`.

### 7.3 `subcategories`

Represents child category cards or child category data for the current page.

For the services page, this is important frontend-facing data.

Typical fields:

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

Do not remove or rename these fields casually.

### 7.4 `blocks`

Represents remaining block payloads that are not routed to `content` or `sections`.

### 7.5 `children`

Represents structural category tree information or compatibility output.

Its current role may overlap with `subcategories`, but it must not be removed without a separate audit.

---

## 8. Layer responsibilities

### 8.1 Controller

Expected role:

```text
- receive route params
- call Repository or appropriate service
- return Resource
```

Should not:

```text
- build EAV payloads
- decide response shape
- perform deep data loading logic
```

### 8.2 Repository / query layer

Expected role:

```text
- perform SQL and Eloquent queries
- apply locale filter
- load root category
- load category blocks/items/propertyValues/property
- load required children/subcategory relations
- prepare enough graph for serialization
```

The Repository may grow large if it becomes responsible for both query and response assembly. If that happens, a small collaborator may be justified.

### 8.3 Resource

Expected role:

```text
- serialize prepared graph
- preserve public response shape
- call EavContentResolver on already-loaded data
- apply compatibility keys
```

Should not:

```text
- call Model::where()
- call Model::with()
- call firstOrFail()
- trigger hidden SQL
- discover missing graph parts by querying during serialization
```

### 8.4 EavContentResolver

Expected role:

```text
- transform loaded BlockItem collections into flat arrays
- cast values by value_type
- handle is_collection
- provide single/keyed/array output modes
```

Should not:

```text
- query database
- know routes
- know frontend pages
- decide category structure
```

### 8.5 BlockAttachMap

Expected role:

```text
- current compatibility policy for routing block outputs
- maps block key to output location
- defines single/keyed behavior for response assembly
```

It should not be migrated to DB metadata as an incidental cleanup. That belongs to a separate architecture proposal/task.

---

## 9. Possible cleaner pipeline

A more maintainable read-side flow may look like this:

```text
BlockCategoryController
  → BlockCategoryRepository
    → prepared category graph
      → optional CategoryPayloadBuilder / ReadModelAssembler
        → BlockCategoryResource
          → JSON
```

There are several possible future approaches:

```text
A. Repository + Resource only
   Keep classes minimal but improve eager loading and serialization boundaries.

B. Repository + small assembler + Resource
   Repository loads data, assembler builds response-ready structure, Resource serializes.

C. Query objects / read models
   Dedicated classes prepare specific endpoint read models.

D. Contract-first API layer
   Define endpoint contracts first, then build backend around them.
```

No approach is automatically correct. The right choice depends on complexity, risk, and actual project constraints.

---

## 10. Refactor rule: improve without breaking

The system already serves real frontend pages. Therefore, refactoring must preserve compatibility.

Do not break:

```text
- route URLs
- Laravel `data` envelope
- public JSON keys
- locale filtering
- frontend-consumed fields
- existing content keys
```

Refactor should improve:

```text
- clarity of responsibilities
- explicitness of data loading
- predictable EAV transformation
- response assembly readability
- future extensibility
```

---

## 11. What not to assume

Do not assume:

```text
- `children` is unused because `subcategories` exists
- `childs` can be renamed because it is grammatically wrong
- `locale` can be renamed to `scope` because frontend prefers that term
- `acticle` can be renamed to `article` without compatibility
- `BlockAttachMap` should be replaced immediately
- a new assembler is always better
- tests exist for every endpoint
```

Ugly names may be compatibility obligations.

---

## 12. Known improvement areas

The following are improvement candidates, not automatic tasks:

```text
1. Resource boundary cleanup
   Move hidden SQL out of Resources.

2. Category endpoint contract stabilization
   Document what the frontend consumes.

3. Read-model / assembler decision
   Decide when response assembly becomes too large for Resource.

4. EAV transformation consistency
   Reduce duplicated flattening logic between Resources and resolver.

5. BlockAttachMap evolution
   Consider future DB metadata for attach/single/keyed behavior.

6. Naming compatibility layer
   Handle childs/children/subcategories, acticle/article, locale/scope/section.

7. Seeder/import pipeline cleanup
   Improve how content enters the DB without changing output.
```

These should be handled through Architecture Improvement Proposals before becoming broad coding tasks.

---

## 13. Read-side refactor decision guide

When planning a refactor, ask:

```text
1. What exact endpoint is affected?
2. What response shape is currently consumed?
3. Which layer currently performs SQL?
4. Which layer currently transforms EAV?
5. Which fields are compatibility keys?
6. Can the Repository prepare enough data safely?
7. Would an assembler reduce complexity or just move it?
8. Can the change be verified against a saved payload?
9. What should remain deliberately unchanged?
```

Only then edit code.

---

## 14. Manual regression reference

For category endpoint refactoring, the first manual regression should be:

```text
GET /en/blocks/categories/services
```

Compare against saved `services.json`.

Check at minimum:

```text
- data envelope exists
- data.key = services
- data.content exists
- data.subcategories exists
- subcategories count is reasonable / unchanged unless justified
- first subcategory still has id, slug, childs
- EAV fields still exist in subcategories
- data.blocks exists
- data.sections exists
- no EAV internals leak
```

If runtime verification is impossible, the agent must clearly say so and provide static analysis only.

---

## 15. Instructions for future agents

When working on Blocks read-side flow:

```text
1. Inspect the full endpoint chain before editing.
2. Do not optimize one method without understanding the response contract.
3. Preserve public JSON shape unless task explicitly allows migration.
4. Treat Resources as serializers, not query builders.
5. Treat Repository/query layer as owner of data loading.
6. Consider an assembler only when it reduces complexity and is justified.
7. Do not mix read-side refactor with seeder/import refactor.
8. Report uncertainty and remaining risks.
```

---

## 16. Summary

The Blocks read-side flow is the backend path that converts flexible EAV storage into frontend-ready JSON.

Its current direction is valid, but the implementation has accumulated transitional debt around data lifting, layer boundaries, naming, and response assembly.

The next generation of refactoring should focus on:

```text
less hidden SQL
clearer data preparation
stable response contracts
explicit compatibility decisions
no accidental frontend breakage
```
