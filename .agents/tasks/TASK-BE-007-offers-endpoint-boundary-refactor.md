# TASK-BE-007 — Offers Endpoint Boundary Refactor

## Status

Follow-up task after `TASK-BE-005`.

## Type

Backend boundary refactor.

## Priority

P2.

## Dependency

Do not run before `TASK-BE-005` is completed and reviewed.

## Source Context

`TASK-BE-004` found that:

```text
BlockCategoryController::offers()
  - queries BlocksCategories directly
  - queries Block directly
  - builds a raw JSON response inside the controller
  - bypasses Repository/Resource boundaries
```

`TASK-BE-005` must lock down the existing flat response shape before this task begins.

## Main Goal

Move `offers()` data loading and response assembly toward the backend architecture boundaries while preserving the current public contract.

Current contract to preserve unless separately approved:

```text
GET /en/blocks/categories/offers/{slug}

response root:
  category
  block
  items
```

Do not migrate this endpoint to a `.data` envelope in this task unless the frontend handoff explicitly approves it.

## Preferred Direction

Move query logic out of the controller into:

```text
BlockCategoryRepository
```

or a small focused query class/helper if justified.

Possible target flow:

```text
BlockCategoryController::offers($locale, $slug)
  -> repository/query layer prepares category/block/items
  -> Resource or explicit response DTO preserves flat current shape
  -> response()->json(...)
```

## Allowed Changes

You may modify:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/* if introducing a focused Offers resource is justified
tests related to offers endpoint only if they need to reflect preserved behavior
```

## Forbidden Changes

Do not:

```text
- change endpoint URL
- require frontend to switch to response.data.data
- rename category/block/items
- change BlockItemResource output unless separately justified
- refactor unrelated category endpoint logic
- change seeders or migrations
```

## Required Validation

Run:

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
```

At minimum, the offers contract tests from BE-005 must pass.

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-007-offers-endpoint-boundary-refactor.md
```

Include:

```text
- files changed
- what moved out of the controller
- what response shape was preserved
- tests run
- frontend impact statement
```

