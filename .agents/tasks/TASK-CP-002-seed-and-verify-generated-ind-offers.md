# TASK-CP-002 — Seed and Verify Generated Individual Offers

## Type

Execution / verification.

## Goal

Seed generated `ind_offers` JSON files and verify that API endpoints return the expected payloads.

## Commands

Run:

```bash
php artisan db:seed --class=BlockForCpDataSeeder
```

Then check endpoints for each generated key:

```text
GET /api/ru/blocks/categories/offers/{key}
GET /api/en/blocks/categories/offers/{key}
```

Use only locales that were actually populated.

## Expected endpoint shape

Flat JSON root:

```json
{
  "category": {},
  "block": {},
  "items": []
}
```

Do not expect Laravel `.data` envelope for this endpoint.

## Verify inside item properties

Check for:

```text
hero
benefits
extras
important
items
includes
acticle
```

## Do not modify

Do not modify backend code to force content to work.

If a property is skipped because it is not registered in `BlockForCpDataSeeder`, report it and propose a separate schema/content contract update.

## Report

Create:

```text
.agents/reports/REPORT-CP-002-seed-and-verify-generated-ind-offers.md
```

Include:

```text
- seed command result
- endpoints checked
- keys visible in API
- missing or skipped properties
- locale coverage
- next recommended content task
```

