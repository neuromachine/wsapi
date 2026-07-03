# TASK-BE-008 — Explicit Resource Serialization Hardening

## Status

Next practical backend refactor task after `TASK-BE-006` and `TASK-BE-007`.

## Canonical File Name

Use this canonical task filename:

```text
.agents/tasks/TASK-BE-008-explicit-resource-serialization-hardening.md
```

If the repository already contains a similarly named BE-008 task, treat this document as the canonical version and either:

```text
- replace the old BE-008 file, or
- keep the old file but copy this content into it, preserving the repository naming convention.
```

Do not create a duplicate BE-008 task with competing scope.

## Type

Backend Resource refactor / API contract hardening.

## Priority

P1.

## Context

The current backend architecture is moving toward this flow:

```text
Controller
  → Repository / Query Layer
    → Prepared model graph / read payload
      → Resource / focused serializer
        → JSON response
```

Earlier tasks established the direction:

```text
BE-006 — removed duplicated API route registration
BE-007 — moved offers endpoint queries and serialization out of the controller
```

This task continues the same practical refactor sequence.

## Problem

`BlockCategoryResource` still relies on broad model serialization through `attributesToArray()` or equivalent broad attribute merging.

This is risky because future columns added to `blocks_categories` may silently leak into the public API response.

The goal is not to change the API response.
The goal is to make the public response shape explicit.

## Main Goal

Replace broad/implicit serialization in `BlockCategoryResource` with explicit public field mapping while preserving the currently consumed API contract.

## Primary Target

```text
app/Http/Resources/BlockCategoryResource.php
```

Inspect but do not broadly rewrite unless needed:

```text
app/Support/CategoryPayloadAssembler.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
app/Repositories/BlockCategoryRepository.php
services.json, if available
```

## Protected Public Contract

For category endpoint responses, preserve these fields under the Laravel Resource `data` envelope:

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

Inside `subcategories[]`, preserve existing legacy/current keys:

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

Do not rename `childs` to `children` in this task.
Do not rename `section` to `locale` or `scope` in this task.

## Desired Implementation Direction

Make `BlockCategoryResource::toArray()` explicitly return a fixed array, for example conceptually:

```php
return [
    'id' => $this->id,
    'key' => $this->key,
    'name' => $this->name,
    'description' => $this->description,
    'content' => $payload['content'],
    'parent_id' => $this->parent_id,
    'created_at' => $this->created_at,
    'updated_at' => $this->updated_at,
    'section' => $locale,
    'sections' => $payload['sections'],
    'subcategories' => $payload['subcategories'],
    'blocks' => BlockResource::collection($this->whenLoaded('blocks')),
    'children' => BlockCategoryStructureResource::collection($this->whenLoaded('childrenRecursive')),
];
```

This is illustrative. Use actual project variables and current implementation.

## Allowed Changes

Allowed:

```text
- modify BlockCategoryResource.php
- make serialization explicit
- add small private helper methods inside BlockCategoryResource if they improve readability
- adjust null-safe handling for loaded relations
- update report file
```

Allowed only if necessary and small:

```text
- minor compatibility helper inside CategoryPayloadAssembler
- minor formatting in BlockResource if directly related to preventing leakage
```

## Forbidden Changes

Do not:

```text
- change endpoint URLs
- change route registration
- refactor offers endpoint again
- change frontend
- change database schema
- change seeders
- change EavContentResolver behavior
- change BlockAttachMap policy
- rename public JSON keys
- remove legacy keys
- introduce a new broad abstraction layer
```

## Manual Verification

At minimum, after the change inspect or verify:

```text
GET /api/en/blocks/categories/services
```

Expected:

```text
- root still has data envelope
- data.key is services
- data.content exists
- data.sections exists
- data.subcategories exists
- data.blocks exists
- data.children exists
- subcategories still expose childs
```

Run if available:

```bash
php artisan route:list
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
```

If tests are failing due to pre-existing BE-005 issues, do not stop the code refactor. Report that runtime validation is partial.

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-008-explicit-resource-serialization-hardening.md
```

Report:

```text
1. Files changed.
2. Which implicit serialization was removed.
3. Exact public fields now mapped explicitly.
4. Response contract preservation statement.
5. Commands run or not run.
6. Remaining risks.
7. Recommended next task.
```

## Success Criteria

```text
- BlockCategoryResource no longer broadly exposes model attributes for the main category response.
- Public response shape remains compatible.
- Diff is focused and easy to review.
- No frontend changes are required.
```

