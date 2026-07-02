# TASK-BE-008 — Explicit Resource Serialization Hardening

## Status

Follow-up task after `TASK-BE-005`.

## Type

Backend Resource contract hardening.

## Priority

P1.

## Dependency

Do not run before `TASK-BE-005` is completed and reviewed.

## Source Context

`TASK-BE-004` found that:

```text
BlockCategoryResource uses attributesToArray()
```

This means future DB column changes may leak into public API responses.

## Main Goal

Replace unsafe broad serialization in `BlockCategoryResource` with explicit public field mapping while preserving the current response shape.

## Protected Contract

For the category endpoint, preserve:

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

Inside `subcategories[]`, preserve:

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

Do not rename legacy fields in this task.

## Allowed Changes

You may modify:

```text
app/Http/Resources/BlockCategoryResource.php
```

Potentially also:

```text
app/Http/Resources/BlockResource.php
```

only if the same `attributesToArray()` leakage is covered by tests and the change is low-risk.

## Forbidden Changes

Do not:

```text
- change repository query strategy
- refactor offers endpoint
- remove legacy keys
- change frontend code
- change DB schema
- change BlockAttachMap policy
- change EavContentResolver behavior
```

## Required Validation

Run:

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
```

The BE-005 category contract tests must pass.

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-008-explicit-resource-serialization-hardening.md
```

Include:

```text
- fields explicitly mapped
- public contract preserved
- tests run
- any intentionally deferred Resource leakage
```

