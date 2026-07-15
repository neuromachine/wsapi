# TASK-CONTENT-001 — Complete EN Service Offers for All Service Categories

## Status

Practical content generation task.

## Priority

High.

## Goal

Complete English service offer packages for all service category JSON files that currently have root `properties` but missing or incomplete `en.properties`.

## Scope

Target files:

```text
storage/app/blocks/items/*.json
```

These files are consumed by:

```text
database/seeders/ServicesBlockSeeder.php
```

## What to do

1. Scan all files in `storage/app/blocks/items/`.
2. Find files with root `properties.items` and no meaningful `en.properties.items`.
3. Add or complete English offer package content.
4. Preserve existing root Russian content.
5. Preserve package count and structure unless a package is obviously malformed.
6. Keep `vi.properties` as `{}` if no Vietnamese translation is requested.

## Output rules

Do not change:

```text
- PHP seeders
- database schema
- API resources
- frontend code
- route files
- existing root ru properties
```

## JSON rules

Follow:

```text
.agents/contracts/SERVICE-OFFERS-EN-CONTENT-CONTRACT.md
```

## Validation

After content changes, report which files were changed and suggest:

```bash
php artisan db:seed --class=ServicesBlockSeeder
```

Then manually inspect relevant endpoints:

```text
GET /api/en/blocks/categories/{categoryKey}
```

## Required report

Create:

```text
.agents/reports/REPORT-CONTENT-001-complete-en-service-offers.md
```

Report must include:

```text
- files scanned
- files changed
- categories still missing EN offers
- assumptions made during translation/adaptation
- seeding command to run
```
