# TASK-SEED-002 — Individual Offers Category and Package Normalization

## Type

Practical content/seeder task.

## Goal

Normalize `ind_offers` content sources so individual commercial proposals can be seeded consistently and later used for dynamic KP / offer pages.

This prepares the ground for future WS calculator and dynamic commercial proposal work.

## Current problem

The content source already contains individual offer files such as:

```text
storage/app/blocks/blocks/items/ind_offers/default.json
storage/app/blocks/blocks/items/ind_offers/visarun_system.json
storage/app/blocks/blocks/items/ind_offers/medical_platform_en.json
```

But the category-level structure is incomplete or missing:

```text
storage/app/blocks/cat/ind_offers.json
```

Also, some item files use root `properties`, while others only contain `en.properties` or `vi.properties`.

## Files to inspect

```text
database/seeders/BlockForCpDataSeeder.php
database/seeders/KpBlockSeeder.php
database/seeders/DatabaseSeeder.php
database/seeders/Helpers/BlockContentHelper.php
storage/app/blocks/categories.json
storage/app/blocks/cat/ind_offers.json
storage/app/blocks/blocks/items/ind_offers/*.json
```

## Required implementation

1. Ensure `ind_offers` is present in the root categories source:

```text
storage/app/blocks/categories.json
```

2. Create or normalize:

```text
storage/app/blocks/cat/ind_offers.json
```

Suggested shape:

```json
{
  "ru": {
    "descr": "Индивидуальные коммерческие предложения WS",
    "content": "Подборки решений и пакетов под конкретные отрасли и задачи."
  },
  "en": {
    "descr": "WS Individual Commercial Offers",
    "content": "Industry-specific digital solution packages and commercial proposal content."
  },
  "vi": {
    "descr": "Đề xuất thương mại cá nhân của WS",
    "content": "Các gói giải pháp kỹ thuật số theo ngành và mục tiêu kinh doanh."
  }
}
```

3. Normalize at least these files if present:

```text
default.json
visarun_system.json
medical_platform_en.json
```

Use this shape:

```json
{
  "key": "...",
  "name": "...",
  "block": "ind_offers",
  "properties": {},
  "en": { "properties": {} },
  "vi": { "properties": {} }
}
```

Root `properties` remains `ru`.

4. Prefer `BlockForCpDataSeeder` as current seeder.

Do not re-enable or rewrite `KpBlockSeeder` unless a strong reason is found. Treat it as legacy unless proven otherwise.

## Required practical content

Add meaningful English data structure for at least one non-English CP source.

Recommended:

```text
medical_platform_en.json already has en.properties: preserve it.
default.json: add en.properties structure.
visarun_system.json: add en.properties structure, even if first pass is partial.
```

## Do not

```text
- rename acticle to article
- remove current Russian content
- change endpoint response shape
- change OffersResource
- change frontend
- build the calculator yet
```

## Expected report

Create:

```text
.agents/reports/REPORT-SEED-002-ind-offers-category-and-package-normalization.md
```

Include:

```text
- files changed
- which CP files were normalized
- locale coverage table ru/en/vi
- whether BlockForCpDataSeeder remains the active path
- any remaining content gaps
```

## Manual verification target

```text
php artisan db:seed --class=BlockForCpDataSeeder
GET /api/en/blocks/categories/offers/{slug}
```

The endpoint must still return flat JSON:

```text
category
block
items
```

