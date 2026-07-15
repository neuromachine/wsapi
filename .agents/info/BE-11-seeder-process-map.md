# BE-11 — Seeder Process Map

## Purpose

Explain which seeders populate which data, which ones are safe to run for content production, and which ones are structural/risky.

---

## 1. DatabaseSeeder active order

```text
1. BlocksMainCategoriesSeeder
2. BlocksServicesCategoriesSeeder
3. BlocksCategoriesSeeder
4. BlockSeeder
5. BlockItemsForCategoriesDesrDataSeeder
6. BlockForPagesDataSeeder
7. BlockForPortfolioDataSeeder
8. BlockForCpDataSeeder
9. BlockItemPropertiesSeeder
10. ServicesBlockSeeder
11. BlocksForMainSectionsSeeder
12. BlocksForNavigationSeeder
```

---

## 2. Safe command set for current content production

Use these when applying latest JSON-only content work:

```bash
php artisan db:seed --class=ServicesBlockSeeder
php artisan db:seed --class=BlockForCpDataSeeder
```

These apply:

```text
service offer EN translations / normalization
individual CP JSON files
```

---

## 3. Seeder-by-seeder map

| Seeder | Purpose | Source | Target tables | Safety | Notes |
|---|---|---|---|---|---|
| `BlocksMainCategoriesSeeder` | Root categories | `categories.json` | `blocks_categories` | Risky | Can be destructive to IDs |
| `BlocksServicesCategoriesSeeder` | Service category tree | `tree.json`, `cat/{slug}.json` | `blocks_categories` | Risky | Manual ID logic; affects hierarchy |
| `BlocksCategoriesSeeder` | Misc/category descriptors | `blocks/categories/*.json` / cat files | `blocks_categories` | Review before run | Category-wide effect |
| `BlockSeeder` | Core block schemas | hardcoded arrays | `blocks`, some dependent tables | Risky | Can truncate/delete dependent data |
| `BlockItemsForCategoriesDesrDataSeeder` | descr_data items | `blocks/blocks/items/descr_data/*.json` | `block_items`, `block_item_property_values` | Moderately safe | Useful for category content if schema exists |
| `BlockForPagesDataSeeder` | pages | `blocks/blocks/items/pages/*.json` | EAV tables | Moderately safe | Uses content JSON and upsert patterns |
| `BlockForPortfolioDataSeeder` | portfolio | portfolio JSON directories | EAV tables | Moderately safe | Requires portfolio structure consistency |
| `BlockForCpDataSeeder` | individual CP / ind_offers | `blocks/blocks/items/ind_offers/*.json` | EAV tables | Safe for content | Current canonical CP seeder |
| `BlockItemPropertiesSeeder` | legacy properties | hardcoded | `block_item_properties` | Review before run | Legacy/schema helper |
| `ServicesBlockSeeder` | service offers packages | `blocks/items/{categoryKey}.json` | EAV tables | Safe for content | Current canonical service offers seeder |
| `BlocksForMainSectionsSeeder` | main sections | `cat/main/*.json` | EAV tables | Moderately safe | Affects home/main page |
| `BlocksForNavigationSeeder` | navigation | navigation JSON files | EAV tables | Moderately safe | Uses scope-based files |

---

## 4. Current helper roles

### `BlockContentHelper`

File-reading bridge for `storage/app/blocks`.

Responsibilities:

```text
- resolve keys from a source directory;
- read JSON content by key;
- provide category/item data to seeders;
- hide Laravel 12 disk path details via Storage::disk('blocks').
```

### `ImportHelper`

Stable write helper.

Known role:

```text
upsertBlock()
getBlockId()
getCategoryId()
upsertProperty()
getPropertyId()
upsertItem()
upsertPropertyValue()
```

Agents should prefer extending or using helper patterns rather than writing duplicate raw DB logic.

---

## 5. Do-not-run-by-default list

Do not run during normal JSON content updates unless the task explicitly requires structural rebuild:

```bash
php artisan db:seed
php artisan db:seed --class=BlockSeeder
php artisan db:seed --class=BlocksMainCategoriesSeeder
php artisan db:seed --class=BlocksServicesCategoriesSeeder
```

Reason:

```text
These may change IDs, delete records, or rebuild structural tables.
```

---

## 6. Recommended seeding verification

After content seeding:

```text
1. Run only the content seeder.
2. Request affected API endpoint.
3. Confirm locale-specific properties are present.
4. Confirm missing/placeholder vi does not generate hollow properties.
5. Confirm no API shape changed.
```

Example:

```bash
php artisan db:seed --class=ServicesBlockSeeder
```

Then:

```text
GET /api/en/blocks/categories/{categoryKey}
```

For CP:

```bash
php artisan db:seed --class=BlockForCpDataSeeder
```

Then:

```text
GET /api/en/blocks/categories/offers/{proposalKey}
```
