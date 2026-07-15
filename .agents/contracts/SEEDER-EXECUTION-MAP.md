# SEEDER-EXECUTION-MAP — Current Seeder Responsibilities

## Purpose

This document maps seeders to their data responsibility and source files.

## Active seeders

| Seeder | Responsibility | Source family | Notes |
|---|---|---|---|
| `BlocksMainCategoriesSeeder` | Root/system categories | likely static/seeder data | Runs first |
| `BlocksServicesCategoriesSeeder` | service category tree | category data | Must precede service item seeding |
| `BlocksCategoriesSeeder` | generic categories from JSON | `blocks/categories/*.json` | Reads entity category files |
| `BlockSeeder` | block definitions | static | Creates `offers`, `descr_data`, section blocks, etc. |
| `BlockItemsForCategoriesDesrDataSeeder` | category/page descr_data items | `blocks/blocks/items/descr_data/*.json` | Supports `ru/en/vi` style |
| `BlockForPagesDataSeeder` | page item content | `blocks/blocks/items/pages/*.json` | separate family |
| `BlockForPortfolioDataSeeder` | portfolio data | `blocks/cat/portfolio/*.json` | supports sections |
| `BlockForCpDataSeeder` | individual CP/industry offers | `blocks/blocks/items/ind_offers/*.json` | main CP seeder |
| `BlockItemPropertiesSeeder` | legacy/static block properties | static | Some overlap with dynamic property upserts |
| `ServicesBlockSeeder` | service category package offers | `blocks/items/{categoryKey}.json` | currently writes `ru` only |
| `BlocksForMainSectionsSeeder` | main page sections | `blocks/cat/main/*.json` | supports sections |
| `BlocksForNavigationSeeder` | navigation block/items | `blocks/blocks/items/navigation/*.json` | per-file `scope` |

## Legacy/commented seeders

```text
BlockItemsSeeder
BlockItemPropertyValuesSeeder
BlocksForPortfolioPropertyValuesSeeder
KpBlockSeeder
```

`KpBlockSeeder` is a legacy path for `ind_offers` via `blocks/items/ind_offers.json`. Prefer `BlockForCpDataSeeder` for current work.

## Immediate correction zone

### ServicesBlockSeeder

Current limitation:

```text
- Reads blocks/items/{categoryKey}.json
- Iterates items[]
- Writes only locale 'ru'
```

Needed:

```text
- Keep existing root properties as ru
- Add support for en.properties and vi.properties
- Optionally support localized item name
```

### BlockForCpDataSeeder

Current capability:

```text
- Reads blocks/blocks/items/ind_offers/*.json
- Iterates ru/en/vi sections
- Writes EAV values into block ind_offers
```

Needed:

```text
- Ensure category ind_offers exists with localized category data
- Normalize CP JSON files to contain explicit locale sections where needed
```

