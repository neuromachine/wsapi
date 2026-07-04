# BE-08 — Content Seed Pipeline

## Status

Canonical architecture document for the WS content data pipeline after the initial backend refactor, seeding inventory, service-offer localization, category-description localization, and CP batch generation.

This document replaces the earlier incomplete idea of “Content Seed Pipeline” with a broader architecture view:

```text
Content source
  -> Seeder/import logic
    -> EAV database storage
      -> Eloquent read model
        -> API assembly / resources
          -> frontend-facing JSON contract
```

---

## 1. System purpose

The WS backend works as a lightweight content platform / headless CMS core for a Vue SPA.

The platform uses:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

These tables allow the project to add new content structures without creating a database migration for every new UI section, offer package, portfolio item, page, commercial proposal, or navigation item.

The content is usually authored as JSON files and imported through Laravel seeders.

---

## 2. Canonical data path

```text
storage/app/blocks/**/*.json
  -> BlockContentHelper
    -> Seeder
      -> ImportHelper / DB writes
        -> EAV tables
          -> Repository eager loading by locale
            -> EavContentResolver / CategoryPayloadAssembler
              -> Resource
                -> API JSON
                  -> Vue stores / components
```

The practical project rule:

```text
Content work should normally modify JSON files.
Backend code changes are required only when the content family, schema keys, or API shape must be extended.
```

---

## 3. Core content families

| Family | Purpose | Source | Seeder | Main API effect |
|---|---|---|---|---|
| Service categories | Service catalog taxonomy and category content | `storage/app/blocks/cat/*.json`, `cat/{group}/*.json`, `tree.json` | `BlocksServicesCategoriesSeeder`, `BlocksCategoriesSeeder`, `BlockItemsForCategoriesDesrDataSeeder` | `GET /api/{locale}/blocks/categories/services`, `subcategories` |
| Service offers | Price/package blocks on service category pages | `storage/app/blocks/items/{categoryKey}.json` | `ServicesBlockSeeder` | `blocks` / `offers` data inside category endpoint |
| Individual CP / ind_offers | Standalone commercial proposal payloads | `storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json` | `BlockForCpDataSeeder` | `GET /api/{locale}/blocks/categories/offers/{proposalKey}` |
| Pages | Static page content | `storage/app/blocks/blocks/items/pages/*.json` | `BlockForPagesDataSeeder` | Page item endpoints / page blocks |
| Portfolio | Portfolio items and project cards | `storage/app/blocks/cat/portfolio/*.json` and related item files | `BlockForPortfolioDataSeeder` | Portfolio category/item endpoints |
| Main sections | Main page sections | `storage/app/blocks/cat/main/*.json` | `BlocksForMainSectionsSeeder` | Main page block composition |
| Navigation | Header/footer navigation entries | `storage/app/blocks/blocks/items/navigation/*.json`, `blocks/navigation.json` | `BlocksForNavigationSeeder` | Navigation payload |
| descr_data | Category/page descriptive EAV item | `storage/app/blocks/blocks/items/descr_data/*.json` | `BlockItemsForCategoriesDesrDataSeeder` | `data.content`, category descriptions, SEO-like payload |

---

## 4. Locale model

Current locale model for most modern JSON content families:

```text
ru -> root properties
en -> en.properties
vi -> vi.properties
```

Example:

```json
{
  "key": "example",
  "name": "Russian name",
  "properties": {
    "title": "Russian title"
  },
  "en": {
    "name": "English name",
    "properties": {
      "title": "English title"
    }
  },
  "vi": {
    "properties": {}
  }
}
```

Rules:

```text
- root properties are the Russian source of truth;
- en.properties contains English payloads;
- vi.properties may be a placeholder when Vietnamese copy is not ready;
- empty locale property objects must be skipped by seeders, not inserted as hollow values;
- no arbitrary locale key should be introduced until supported by seeder code or helper configuration.
```

---

## 5. Seeder safety classes

### Safer content seeders

These are intended for repeated content updates when the base schema already exists:

```text
ServicesBlockSeeder
BlockForCpDataSeeder
BlockForPagesDataSeeder
BlockForPortfolioDataSeeder
BlocksForMainSectionsSeeder
BlocksForNavigationSeeder
BlockItemsForCategoriesDesrDataSeeder
```

They generally use `updateOrInsert`, `upsertItem`, or controlled upsert-like logic.

### Risky / structural seeders

These should not be run casually during content production:

```text
BlocksMainCategoriesSeeder
BlocksServicesCategoriesSeeder
BlocksCategoriesSeeder
BlockSeeder
```

Risks include manual ID assumptions, truncation/deletion, and schema reset behavior.

---

## 6. Current production status after CONTENT-001..004

Completed content-production work:

```text
CONTENT-001:
  all 61 service offer files received en.name / en.properties payloads.

CONTENT-002:
  all 61 service offer files were normalized for package quality.
  The recommended package is the only featured package.
  Duplicate features and formatting inconsistencies were removed.

CONTENT-003:
  83 category description JSON files received / improved en.properties.descr and en.properties.content.
  _blank.json was skipped as a hollow template.

CONTENT-004:
  6 Vietnam tourism CP JSON files were generated for ind_offers.
  RU and EN payloads are complete; VI remains placeholder.
```

Recommended seeding commands after this work:

```bash
php artisan db:seed --class=ServicesBlockSeeder
php artisan db:seed --class=BlockForCpDataSeeder
```

Category description updates may require category/descr_data seeders, but structural seeders must be treated carefully because the inventory found destructive/idempotency risks.

---

## 7. Relationship to frontend

The frontend should receive denormalized JSON. It should not know about:

```text
property_id
item_id
block_item_property_values.id
value_type internals
EAV joins
seeder source file paths
```

The backend read-side resolves EAV into logical content objects.

Standard category endpoint:

```text
GET /api/{locale}/blocks/categories/{slug}
```

returns a Laravel Resource envelope:

```json
{
  "data": {
    "id": 2,
    "key": "services",
    "content": {},
    "subcategories": [],
    "blocks": [],
    "sections": []
  }
}
```

Individual offer endpoint:

```text
GET /api/{locale}/blocks/categories/offers/{proposalKey}
```

is intentionally flat:

```json
{
  "category": {},
  "block": {},
  "items": []
}
```

This asymmetry is currently part of the public contract and must not be changed without frontend handoff.

---

## 8. Agent rules

When adding or changing content:

```text
- prefer JSON changes;
- preserve legacy keys: childs, acticle, section;
- preserve locale model: root properties for ru, {locale}.properties for non-RU;
- do not invent unsupported property keys;
- do not run structural/destructive seeders unless explicitly requested;
- verify via the specific endpoint affected by the content family;
- document changed files and seed command.
```
