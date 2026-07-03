# TASK-BE-010 — EAV Resolver / BlockItemResource Consistency

## Status

Follow-up practical backend refactor after BE-009.

## Type

Transformation consistency cleanup.

## Priority

P2.

## Problem

The project has more than one place that can flatten or cast EAV data:

```text
app/Support/EavContentResolver.php
app/Http/Resources/BlockItemResource.php
```

If these two paths cast JSON, numbers, booleans, collections, or property keys differently, API responses can become inconsistent.

## Main Goal

Make EAV transformation behavior consistent between category payload assembly and individual item resources.

Do this with the smallest safe code change.

## Primary Targets

```text
app/Support/EavContentResolver.php
app/Http/Resources/BlockItemResource.php
```

Inspect:

```text
app/Support/CategoryPayloadAssembler.php
app/Http/Resources/BlockResource.php
app/Repositories/BlockItemRepository.php
app/Repositories/BlockCategoryRepository.php
```

## Desired Direction

Prefer one canonical EAV flattening/casting path.

Likely preferred direction:

```text
EavContentResolver becomes the canonical transformation helper.
BlockItemResource uses either EavContentResolver or mirrors its behavior exactly with minimal duplication.
```

Do not over-abstract.
Do not introduce a large service layer.

## Required Analysis Before Editing

Compare:

```text
- how EavContentResolver reads propertyValues
- how BlockItemResource groups property values
- which type marker is used: value_type vs property.type
- how json values are decoded
- how is_collection is handled
- whether item key/id/name are preserved
```

## Allowed Changes

Allowed:

```text
- update BlockItemResource to use EavContentResolver for properties if safe
- add a focused public method to EavContentResolver if needed
- remove duplicate casting code if behavior is preserved
- add compatibility comments
- update report
```

## Forbidden Changes

Do not:

```text
- change public API field names
- remove properties object from BlockItemResource
- change category endpoint response shape
- change offers endpoint flat shape
- change database schema
- change frontend
- rewrite EavContentResolver into a service class
```

## Compatibility Requirements

Preserve item-level output keys such as:

```text
id
key
name
description
properties
```

Inside `properties`, preserve logical property keys:

```text
title
descr
content
metadata
price
features
hero
benefits
includes
items
important
extras
```

Do not leak raw EAV internals unless they already existed and are intentionally preserved.

## Manual Verification

Check:

```text
GET /api/en/blocks/items/{slug}
GET /api/en/blocks/categories/services
GET /api/en/blocks/categories/offers/{slug}
```

Expected:

```text
- item properties are still present
- json values still decode where expected
- collections still appear as arrays
- category endpoint still has content/subcategories/sections
- offers endpoint still flat category/block/items
```

Run if available:

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
```

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-010-eav-resolver-block-item-resource-consistency.md
```

Include:

```text
1. Current inconsistency found.
2. Chosen canonical transformation path.
3. Files changed.
4. Public response compatibility statement.
5. Commands run or not run.
6. Remaining EAV debt.
7. Recommended next task.
```

## Success Criteria

```text
- EAV flattening/casting is less duplicated.
- BlockItemResource and category payload logic behave consistently.
- Public API remains compatible.
- Diff stays focused.
```

