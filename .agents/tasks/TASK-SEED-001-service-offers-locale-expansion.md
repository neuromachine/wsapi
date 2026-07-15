# TASK-SEED-001 — Service Offers Locale Expansion

## Type

Practical seeder/data task.

## Goal

Make service package offers usable for non-RU locales, especially `en`, without changing API response shape.

Current problem:

```text
ServicesBlockSeeder imports service package offers only as locale 'ru'.
```

This means `GET /api/en/...` category data may not receive service offer package EAV values for block `offers`.

## Files to inspect

```text
database/seeders/ServicesBlockSeeder.php
database/seeders/Helpers/BlockContentHelper.php
database/seeders/Helpers/ImportHelper.php
storage/app/blocks/items/*.json
storage/app/blocks/cat/services.json
storage/app/blocks/cat/*.json
```

## Expected data source format

Current legacy root format must remain supported:

```json
{
  "items": [
    {
      "name": "Базовый",
      "key": "landing_basic",
      "properties": {
        "title": "...",
        "descr": "..."
      }
    }
  ]
}
```

Add support for localized item sections:

```json
{
  "items": [
    {
      "name": "Базовый",
      "key": "landing_basic",
      "properties": {},
      "en": {
        "name": "Basic",
        "properties": {}
      },
      "vi": {
        "name": "...",
        "properties": {}
      }
    }
  ]
}
```

Root `properties` remains `ru`.

## Required implementation

Update `ServicesBlockSeeder` so it:

```text
1. keeps reading current categoryKeys list or improves it only if safe;
2. keeps root properties as ru;
3. reads en.properties and vi.properties when present;
4. uses ImportHelper::upsertPropertyValue($itemId, $propertyId, $section, $propValue);
5. optionally updates item name from localized name only if this does not break current item key identity;
6. does not remove existing ru data.
```

## Data changes

Add/normalize at least two English service offer package examples in source JSON.

Recommended priority:

```text
storage/app/blocks/items/posadocnaia-stranica.json
storage/app/blocks/items/internet-katalog.json
```

If those files are absent, choose the first existing high-priority service package source files.

## Do not

```text
- change frontend
- change API Resource/Repository
- rename offer item keys
- migrate to a new endpoint
- change flat offers endpoint
- touch ind_offers in this task except if documenting relationship
```

## Expected report

Create:

```text
.agents/reports/REPORT-SEED-001-service-offers-locale-expansion.md
```

Include:

```text
- files changed
- which source files now include en/vi structure
- how ServicesBlockSeeder reads locales
- commands run or not run
- manual verification URLs
```

## Manual verification target

```text
php artisan db:seed --class=ServicesBlockSeeder
GET /api/en/blocks/categories/{serviceCategorySlug}
```

