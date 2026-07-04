# Seeding Layer Inventory

This document maps the execution flow, dependencies, and behavior of the Laravel seeding layer in the WS project.

## 1. Execution Order (DatabaseSeeder)

The `DatabaseSeeder` acts as the orchestrator and executes seeders in the following order:

1. `BlocksMainCategoriesSeeder` - Creates root categories (main).
2. `BlocksServicesCategoriesSeeder` - Creates service categories from `tree.json`.
3. `BlocksCategoriesSeeder` - Creates other block categories from `blocks/categories/*.json`.
4. `BlockSeeder` - Defines the core block schemas (e.g., `offers`, `pages`, `portfolio`, `ind_offers`) and truncates dependent tables.
5. `BlockItemsForCategoriesDesrDataSeeder` - Seeds `descr_data` items for categories.
6. `BlockForPagesDataSeeder` - Seeds the `pages` block items.
7. `BlockForPortfolioDataSeeder` - Seeds the `portfolio` block items.
8. `BlockForCpDataSeeder` - Seeds the `ind_offers` block items.
9. `BlockItemPropertiesSeeder` - Seeds legacy properties.
10. `ServicesBlockSeeder` - Seeds the `offers` block items (service packages).
11. `BlocksForMainSectionsSeeder` - Seeds the `main` category items.
12. `BlocksForNavigationSeeder` - Seeds the `navigation` block items.

## 2. Seeder Details

### BlocksMainCategoriesSeeder
- **Source**: `storage/app/blocks/categories.json`
- **Tables Affected**: `blocks_categories`
- **Idempotency**: Truncates `blocks_categories` initially (destructive to IDs). Does not use `updateOrInsert`.
- **Locale Handling**: Locales are not explicitly parsed; the seeder relies on the JSON structure which lacks strict locale divisions for roots.
- **API Impact**: Changes ID mappings which can break foreign keys if not handled via cascading.

### BlocksServicesCategoriesSeeder
- **Source**: `storage/app/tree.json` and `cat/{slug}.json` via `BlockContentHelper`
- **Tables Affected**: `blocks_categories`
- **Idempotency**: Uses manual auto-increment logic starting from ID 1000 without `updateOrInsert`. If run twice without deletion, it can cause constraint violations or duplicate keys.
- **Locale Handling**: Relies entirely on the underlying `cat/{slug}.json` parsing in `BlockContentHelper`, though category records themselves don't persist locale in the table.
- **API Impact**: Determines the category hierarchy (`subcategories` response).

### BlockSeeder
- **Source**: Hardcoded arrays inside the seeder.
- **Tables Affected**: `blocks`, `block_items`, `block_item_property_values` (truncates them).
- **Idempotency**: Destructive. Uses `delete()` and manual `insert()`. 
- **Locale Handling**: None, defines schema metadata.
- **API Impact**: Defines the core keys (`pages`, `portfolio`, `ind_offers`) exposed to the API.

### BlockForCpDataSeeder (ind_offers)
- **Source**: `storage/app/blocks/blocks/items/ind_offers/*.json`
- **Tables Affected**: `block_items`, `block_item_properties`, `block_item_property_values`
- **Idempotency**: Uses `ImportHelper::upsertItem` and `upsertPropertyValue`, safe to re-run.
- **Locale Handling**: Explicitly loops over `['ru', 'en', 'vi']`. Falls back to `ru` structure if keys are missing but scopes exist.
- **API Impact**: Directly populates the CP / individual offers payload.

### ServicesBlockSeeder (offers)
- **Source**: `storage/app/blocks/items/{category_key}.json`
- **Tables Affected**: `block_items`, `block_item_property_values`
- **Idempotency**: Uses `ImportHelper::upsertItem`, safe to re-run.
- **Locale Handling**: Explicitly loops over `['ru', 'en', 'vi']`. If a locale has an empty `properties` object, it skips creating values for that locale.
- **API Impact**: Populates the service offers packages endpoint.

### Content-oriented Seeders (Pages, Portfolio, DescrData, MainSections, Navigation)
- **Source**: Distinct JSON directories (e.g. `blocks/items/pages/`, `blocks/portfolio/`).
- **Tables Affected**: `block_items`, `block_item_property_values`
- **Idempotency**: Generally use `ImportHelper::upsertItem`, though schema definitions within them might overlap.
- **Locale Handling**: Hardcoded iteration over `['ru', 'en', 'vi']` or relying on `$data['scope']` (for navigation).
