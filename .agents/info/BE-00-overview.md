# BE-00 — Backend Overview

## Status

Draft context document for the backend column of the WebSolutions architecture.

This document is not a task specification and not an `.agents` package. It defines the current backend context, the accepted refactoring direction, and the boundaries that future agent tasks must respect.

## Scope of this draft

This draft covers the backend read-side path used by the frontend page:

```text
Frontend route:
  /en/services

API request:
  GET /en/blocks/categories/services

Backend path:
  routes/api.php
    -> BlockCategoryController::index($locale, $slug)
      -> BlockCategoryRepository::getCategory($locale, $slug)
        -> BlockCategoryResource
          -> JSON Resource response
```

The current practical focus is the endpoint behind the **Services** page and the boundary between:

```text
Repository
  prepares the model graph

Resource
  serializes the already-prepared model graph
```

## Current architectural position

The backend is a Laravel API serving a Vue SPA. The backend is not a simple CRUD layer. It behaves as a headless content/API system with a block/category/EAV content model.

Current core flow:

```text
HTTP request
  -> Controller
    -> Repository
      -> Eloquent models and relations
        -> Resource
          -> JSON payload
            -> Vue / Pinia frontend
```

The target architectural rule is:

```text
Repository prepares data.
Resource serializes data.
EavContentResolver transforms EAV items.
BlockAttachMap routes block output.
```

## Known backend facts

### Runtime / framework

Known:

```text
Laravel 12 API
PHP 8.x project line
Eloquent ORM
API Resources
Filament exists in project, but is not part of the current read-side task focus
```

### Main module

The current focus is the `blocks` module.

Relevant files:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
```

### Data model

Known main tables:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

Logical layers:

```text
STRUCTURE LAYER
  blocks
  blocks_categories

ENTITY LAYER
  block_items

SCHEMA LAYER
  block_item_properties

DATA LAYER
  block_item_property_values
```

### API response convention

Frontend expects Laravel Resource payloads through:

```js
response.data.data
```

For the control endpoint, the payload shape currently includes:

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
```

For `/en/blocks/categories/services`, the current payload has:

```text
content      -> object with title, descr, content, metadata, priority
sections     -> currently empty array
subcategories -> list of service direction categories
blocks       -> list containing at least descr_data block output
```

## Current control example

Control endpoint:

```text
GET /en/blocks/categories/services
```

The current saved payload should be treated as a regression reference for the first backend refactor pass.

Important public keys to preserve for this endpoint:

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

Important `subcategories[]` keys to preserve:

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

Important compatibility note:

```text
subcategories is required by the current Services page.
childs is a legacy spelling but must not be renamed in the first pass.
children is unresolved/legacy-recursive context and must not be removed without a separate audit.
```

## Known problem

The current `BlockCategoryResource` violates the target boundary.

Specifically, `resolveSubitems($locale)` performs database work from inside a Resource:

```text
BlocksCategories::where(...)
BlocksCategories::with(...)
```

This means the Resource is not only serializing the prepared model. It is completing the data loading itself.

Current problem formula:

```text
Repository partially prepares category graph.
Resource discovers that child categories are not prepared enough.
Resource performs extra SQL to finish the job.
```

Target formula:

```text
Repository prepares complete category graph.
Resource serializes already-loaded relations only.
```

## Accepted first refactor direction

Accepted direction: **Variant A — minimal and safest surgical pass**.

Intent:

```text
Move child/subcategory data preparation into BlockCategoryRepository.
Keep BlockCategoryResource focused on serialization.
Preserve current public JSON shape.
Do not change database schema.
Do not change routes.
Do not rename public response keys.
Do not refactor the entire blocks module.
```

Expected first practical target:

```text
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
```

Possible supporting files for reading only:

```text
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
services.json
```

## What is known vs assumed

### Known

```text
- /en/services requests /en/blocks/categories/services.
- BlockCategoryRepository::getCategory() loads the root category, blocks, block items, property values, and direct children.
- BlockCategoryResource::resolveSubitems() currently performs additional SQL for each child category.
- services.json contains a current public response shape that must be preserved for the first pass.
- EavContentResolver is the central EAV -> flat array transformer.
- BlockAttachMap decides whether block output goes to content, sections, or blocks.
```

### Assumed / to verify during practical work

```text
- The first safe fix can be done by extending eager loading in BlockCategoryRepository.
- children vs subcategories vs childs naming contains legacy debt.
- childrenRecursive is not required for the first pass of /services but must not be removed.
- A new assembler/helper is not needed for the first surgical pass unless the minimal repository/resource split becomes unreadable.
- Feature tests are optional at this stage; manual endpoint comparison is acceptable for the first agent run.
```

## Out of scope for this draft

Do not expand this draft into:

```text
- concrete task file
- `.agents` package
- DB migration proposal
- BlockAttachMap database migration
- full backend service layer redesign
- frontend changes
- form subsystem refactor
- admin/Filament architecture
```

## Refactor posture

Current policy:

```text
Everything can be changed eventually, but not simultaneously.
Each change needs a reason, risk level, and rollback boundary.
```

For the first backend pass:

```text
Preserve response shape first.
Restore layer boundary second.
Improve naming/schema only in separate tasks.
```
