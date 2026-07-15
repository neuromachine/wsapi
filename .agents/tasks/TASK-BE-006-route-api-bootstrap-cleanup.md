# TASK-BE-006 — Route and API Bootstrap Cleanup

## Status

Follow-up task after `TASK-BE-005`.

## Type

Small backend cleanup / routing safety.

## Priority

P1.

## Dependency

Do not run before `TASK-BE-005` is completed and reviewed.

## Source Context

`TASK-BE-004` found duplicated API route registration:

```text
bootstrap/app.php registers routes/api.php
AppServiceProvider::boot() also manually registers routes/api.php with api prefix
```

This may cause duplicate route loading or double-prefixing.

## Main Goal

Verify actual route registration behavior and remove redundant API route registration if confirmed safe.

## Required Investigation

Inspect:

```text
bootstrap/app.php
app/Providers/AppServiceProvider.php
routes/api.php
php artisan route:list
```

Before editing, capture route list evidence for relevant endpoints:

```text
/en/blocks/categories/services
/en/blocks/categories/offers/{slug}
/en/forms/submit
```

## Expected Change

If duplication is confirmed, remove manual API route registration from:

```text
app/Providers/AppServiceProvider.php
```

Do not change `routes/api.php` endpoint definitions unless necessary.

## Forbidden Changes

Do not:

```text
- change endpoint URLs
- change locale prefix behavior
- refactor controllers
- refactor resources
- change frontend code
- change tests except for updating route assertions if needed
```

## Required Validation

Run:

```bash
php artisan route:list
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
```

## Expected Report

Create or update:

```text
.agents/reports/REPORT-BE-006-route-api-bootstrap-cleanup.md
```

Include before/after route evidence.

