# TASK-BE-001 — BlockCategoryResource Boundary Refactor

## Task type

Surgical backend refactor.

## Goal

Remove SQL/model querying from:

```text
app/Http/Resources/BlockCategoryResource.php
```

specifically from the subcategory construction flow, while preserving the current public JSON response shape for:

```text
GET /en/blocks/categories/services
```

The desired architecture is:

```text
BlockCategoryRepository prepares the model graph.
BlockCategoryResource serializes already prepared data.
EavContentResolver transforms already loaded EAV collections.
```

## Control endpoint

Use this endpoint as the control/reference chain:

```text
Frontend route: /en/services
API request:   GET /en/blocks/categories/services
```

The saved `services.json` payload is the regression reference for the public response shape.

## Problem statement

`BlockCategoryResource` currently forms the category response, including:

```text
content
sections
subcategories
blocks
children
```

But the current `resolveSubitems()` implementation performs extra database/model work from inside the Resource layer.

This violates the accepted backend boundary:

```text
Repository = SQL, eager loading, locale filtering, relation preparation.
Resource   = serialization only.
```

## Primary target files

Inspect and modify only what is necessary:

```text
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
```

## Supporting files to inspect before editing

```text
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
services.json
.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md
.agents/info/BE-RESOURCE-BOUNDARY.md
```

Do not refactor supporting files unless strictly required for the primary goal.

## Required behavior to preserve

The response for `GET /en/blocks/categories/services` must remain compatible with the current `services.json` shape.

At minimum, preserve these top-level keys when currently present:

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

Preserve `subcategories` as a public contract.

For `subcategories[]`, preserve existing public field names, including legacy names:

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
Do not remove `children` from the top-level response in this task.
Do not collapse `subcategories` into `children` in this task.

## Accepted implementation direction

Use Variant A: minimal and safest.

Expected direction:

```text
1. Extend or adjust BlockCategoryRepository so the category model returned by getCategory()
   contains enough preloaded child/subcategory data.

2. Keep locale filtering in the repository layer.

3. Update BlockCategoryResource so resolveSubitems() works only with already loaded relations/data.

4. Remove direct model querying from BlockCategoryResource.

5. Preserve existing JSON keys and data meaning.
```

The repository may use additional eager loading for child categories, their relevant items, propertyValues, and properties if that is the smallest safe way to prepare the response.

A small private repository helper is allowed if it improves readability.
A new helper/assembler class is allowed only if clearly justified and small. Do not introduce a broad service layer.

## Hard constraints

Do not change:

```text
routes/api.php
database migrations
database schema
frontend files
public JSON key names
BlockAttachMap behavior
EavContentResolver behavior
unrelated resources
seeders
```

Do not perform a global cleanup.
Do not rewrite the blocks module.
Do not redesign the API contract.

## Resource-layer guardrail

After the change, `BlockCategoryResource.php` must not contain category SQL/model discovery such as:

```php
BlocksCategories::where(...)
BlocksCategories::with(...)
```

Prefer removing the `use App\Models\BlocksCategories;` import from the Resource if it becomes unused.

## Manual regression checklist

Before completing the task, report the result of these checks.

### Code boundary checks

```text
- BlockCategoryResource does not query BlocksCategories.
- resolveSubitems() uses already loaded relations/data.
- BlockCategoryRepository is responsible for needed loading/filtering.
- BlockAttachMap was not redesigned.
- EavContentResolver was not redesigned.
```

### Response compatibility checks

Compare `GET /en/blocks/categories/services` before/after if the project can be run locally.

If the project cannot be run, perform static comparison against `services.json` and state that runtime verification was not executed.

Check:

```text
- data.content exists and keeps the same structure.
- data.sections exists.
- data.subcategories exists.
- data.subcategories[] keeps id, slug, childs, title, descr, content, metadata, priority where available.
- data.blocks exists and keeps the same structural role.
- data.children is not removed.
```

### Optional commands

Run only if available and not disruptive:

```bash
php -l app/Repositories/BlockCategoryRepository.php
php -l app/Http/Resources/BlockCategoryResource.php
php artisan test
```

If tests are not configured or not relevant, do not invent a test suite. Report that manual regression was used.

## Done when

The task is complete when:

```text
- SQL/model querying is removed from BlockCategoryResource subcategory flow.
- The repository prepares the required category/subcategory data.
- The public response contract for /en/blocks/categories/services is preserved.
- The diff is narrow and justified.
- Manual regression notes are provided.
```

## Final report format

At the end of the run, provide:

```text
1. Files changed
2. What changed
3. Why this preserves the contract
4. What was intentionally not changed
5. Manual regression result
6. Remaining risks / next recommended step
```

## Important reminder

This task is not a full backend redesign.

The correct outcome is a safer boundary around the existing behavior, not a new API architecture.
