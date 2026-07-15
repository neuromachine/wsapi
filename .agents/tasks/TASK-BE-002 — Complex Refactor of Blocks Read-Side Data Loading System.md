# TASK-BE-002 — Complex Refactor of Blocks Read-Side Data Loading System

## Status

Primary backend refactoring task.

## Priority

High.

## Core Principle

Improve without breaking.

This is not a rewrite for the sake of rewrite.
This is a structural refactoring of the existing backend read-side system to reduce “костыльный” data lifting, clarify responsibilities, and make future growth safer.

The current public API must remain compatible unless a breaking change is explicitly proposed, justified, and left for a separate approved task.

---

## Context

The project uses a Laravel API backend for a Vue SPA frontend.

The current backend content system is built around:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

The system behaves as a headless content/API layer with EAV-style dynamic content.

The known problematic area is the “подъем данных” for category endpoints, especially:

```text
GET /{locale}/blocks/categories/{slug}
GET /en/blocks/categories/services
```

The current implementation has grown organically. Some Resources perform work that should likely belong to Repository / query / assembler / read-model layers. Some response structures are frontend-facing and must not be broken accidentally.

The earlier narrow attempt focused only on moving SQL out of `BlockCategoryResource::resolveSubitems()`. That was too narrow and may have overfit to a presumed need. This task takes a broader view.

---

## Main Goal

Refactor the backend read-side data loading and serialization system for Blocks/Categories so that it becomes more coherent, maintainable, and scalable, while preserving the currently consumed API response shape.

The agent must analyze the full flow before editing.

---

## Primary Target Area

Focus on the backend read-side flow around:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Models/BlocksCategories.php
app/Models/Block.php
app/Models/BlockItem.php
app/Models/BlockItemProperty.php
app/Models/BlockItemPropertyValue.php
```

Secondary files may be touched only if justified.

---

## Reference Endpoint

Use this as the first regression reference:

```text
GET /en/blocks/categories/services
```

Reference payload:

```text
services.json
```

The response shape must remain compatible with this payload.

---

## Existing Shape Must Be Preserved

Do not casually rename or remove:

```text
data
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

Inside `subcategories`, preserve currently consumed keys such as:

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

Even if some names are imperfect, they are part of the current contract until a separate migration task is approved.

---

## What Must Be Investigated

Before editing, inspect and reason about:

```text
1. Which layer currently performs DB queries.
2. Which layer performs EAV transformation.
3. Which layer decides what becomes content / sections / blocks / subcategories.
4. Which parts of the response are public API contract.
5. Which methods are legacy compatibility.
6. Which structures are actually consumed by the frontend.
7. Which parts can be safely improved now.
8. Which parts should be documented but not changed yet.
```

The agent must not assume that a field is unused just because it looks ugly or redundant.

---

## Desired Direction

Move the system toward a clearer pipeline:

```text
Controller
  → Repository / Query Layer
    → Prepared category graph
      → Optional ReadModel / Assembler / Payload Builder
        → Resource serialization
          → JSON response
```

The exact implementation is not prescribed.

The agent may choose one of these approaches if justified:

```text
A. Keep Repository + Resource only, but clean responsibilities.
B. Introduce a small read-model / assembler / payload builder.
C. Add focused helper classes for category response assembly.
D. Improve model relations and eager-loading strategy.
```

The chosen approach must be explained before or within the final report.

---

## Architectural Boundaries

### Controller

Controller should stay thin.

Allowed:

```text
- receive route params
- call repository/service
- return resource/response
```

Not allowed:

```text
- complex query logic
- EAV transformation
- response assembly details
```

---

### Repository / Query Layer

Repository or query-related classes may handle:

```text
- SQL
- Eloquent query construction
- eager loading
- locale filtering
- recursive/category tree loading
- relation completeness
- sorting where appropriate
- returning prepared models or read structures
```

The Repository should not blindly become a God object. If the logic becomes too large, introduce a small named collaborator.

---

### Resource Layer

Resource should primarily handle serialization.

Allowed:

```text
- convert prepared model/read structure into API array
- call pure transformation helpers on already-loaded data
- preserve compatibility keys
- perform simple null-safe formatting
```

Not allowed:

```text
- new SQL queries
- Model::where()
- Repository calls
- hidden eager loading
- business filtering
- changing response shape without approval
```

---

### EavContentResolver

`EavContentResolver` should remain a focused transformation layer.

It may be improved if necessary, but only if:

```text
- behavior stays compatible
- changes are justified
- usage sites are checked
```

Avoid turning it into a query service.

---

### BlockAttachMap

`BlockAttachMap` may be analyzed and documented.

Do not migrate it to DB metadata in this task unless there is a very strong, low-risk reason.

Prefer to treat it as current compatibility policy.

---

## Acceptable Changes

The agent may:

```text
- move DB queries out of Resources
- improve eager loading
- introduce a small assembler/read-model class
- split large private methods if it improves clarity
- make API response assembly more explicit
- reduce duplicated transformation logic
- add comments only where they clarify transitional compatibility
- add minimal regression checks or helper scripts if already consistent with the project
- add manual regression instructions
```

---

## Forbidden Changes

Do not:

```text
- change database schema
- rename public JSON keys
- remove subcategories / children / childs
- change frontend code
- rewrite all Resources at once without need
- replace the architecture with a new framework-style abstraction
- add a large service layer without clear necessity
- change routes/api.php
- change endpoint URLs
- change locale/scope behavior
- change seed content
- rewrite BlockAttachMap into DB-driven config in this task
```

---

## Refactoring Strategy

Use a staged approach.

### Stage 1 — Inventory

Inspect current files and produce a mental map:

```text
Endpoint → Controller → Repository → Model Relations → Resource → Support classes → JSON
```

Identify:

```text
- hidden SQL in Resources
- duplicated EAV transformation
- implicit public contract leaks
- unsafe attributesToArray usage
- sorting and priority logic
- frontend contract dependencies
```

### Stage 2 — Target Design

Choose the smallest design that makes the system cleaner.

Prefer:

```text
- explicit responsibility boundaries
- compatibility layer instead of breaking rename
- small named collaborators instead of massive methods
- no schema changes
```

### Stage 3 — Implementation

Apply refactor in coherent steps.

The implementation should improve the system, not merely move code around.

### Stage 4 — Regression

Validate manually or with existing project commands.

At minimum compare:

```text
GET /en/blocks/categories/services
```

before/after against `services.json`.

---

## Manual Regression Checklist

After changes, verify:

```text
- endpoint still returns Laravel Resource envelope: data
- data.content still exists and has expected fields
- data.subcategories still exists
- subcategories count is not unexpectedly reduced
- subcategories items still include id, slug, childs
- subcategories EAV fields still exist: title, descr, content, metadata, priority
- data.blocks still exists
- data.sections still exists
- data.children still exists or remains compatible
- no EAV internals leak into API response
- no Resource performs direct SQL queries
- frontend route /en/services can still consume the response
```

---

## Expected Deliverables

The final agent report must include:

```text
1. Files changed.
2. What was structurally improved.
3. What public API shape was preserved.
4. What was intentionally not changed.
5. Any remaining architectural debt.
6. Manual regression result or exact reason it could not be run.
7. Recommended next task.
```

---

## Success Criteria

This task is successful if:

```text
- data lifting is cleaner and easier to understand
- Resources no longer compensate for missing Repository preparation
- public API shape is preserved
- /en/blocks/categories/services remains compatible
- the system is easier to extend for future loaded category/page structures
- changes are justified and limited to backend read-side concerns
```

---

## Failure Criteria

This task fails if:

```text
- frontend response shape is broken
- public keys are renamed without compatibility
- Resource still performs DB queries after refactor
- broad unrelated files are rewritten
- database schema is changed
- the task becomes a full rewrite instead of a refactor
- the final result is harder to understand than the original
```

---

## Core Reminder

The goal is not to make the system theoretically perfect.

The goal is to improve the existing system under real project constraints:

```text
less hidden behavior
less layer confusion
less duplicated lifting logic
more explicit contracts
more maintainable read-side flow
no broken frontend
```
