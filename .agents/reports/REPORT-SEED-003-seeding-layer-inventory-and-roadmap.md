# REPORT-SEED-003: Seeding Layer Inventory and Roadmap

## 1. Executive Summary

The seeding layer in WS provides a bridge between JSON file definitions and the EAV-style `blocks` database architecture. While functional and modular, the layer suffers from inconsistencies in idempotency, hardcoded locale handling, and destructive operations that complicate partial updates. 

This report outlines the current state and proposes a non-breaking roadmap to harden the seeding architecture, ensuring it supports incremental content scaling (like CP and Service Offers) without wiping out IDs or breaking relations.

## 2. Key Findings

### Gaps and Weaknesses
- **Destructive Category Seeding**: `BlocksServicesCategoriesSeeder` manually assigns IDs (starting at 1000) and `BlocksMainCategoriesSeeder` uses `delete()`. This breaks foreign key integrity if `block_items` are not also truncated, and prevents safe incremental updates.
- **Schema Destructiveness**: `BlockSeeder` manually drops `blocks`, `block_items`, and `block_item_property_values`. This makes it impossible to run `php artisan db:seed` safely in production to just add a new block.
- **Hardcoded Locales**: The locale list `['ru', 'en', 'vi']` is hardcoded across multiple seeders (`ServicesBlockSeeder`, `BlockForPagesDataSeeder`, `BlockForCpDataSeeder`, etc.). Adding a new language requires updating multiple PHP files instead of a single configuration or helper.
- **Inconsistent JSON Parsing Paths**: `BlockContentHelper` relies on strict directory structures (`cat/`, `blocks/items/`, `blocks/blocks/items/`) which sometimes overlap or create confusion (e.g., `ind_offers` path vs `pages` path).
- **Missing Validation**: Seeders assume JSON structures are perfectly formed. `ServicesBlockSeeder` gracefully skips empty properties, but other seeders might fail or insert nulls if a key is missing.

### Duplication
- **Property Upserting**: The logic to upsert block properties (e.g. `content`, `title`, `descr`) is duplicated across `BlockForPagesDataSeeder`, `BlockForPortfolioDataSeeder`, and `BlockForCpDataSeeder`.
- **Locale Loops**: The `foreach (['ru', 'en', 'vi'] as $locale)` block is repeated verbatim in at least 4 different seeders.

## 3. Recommended Roadmap

To address these issues without breaking the frontend or API contracts, the following staged improvements are recommended:

### Phase 1: Idempotency & Safety (High Priority)
- Refactor `BlocksServicesCategoriesSeeder` and `BlocksMainCategoriesSeeder` to use `updateOrInsert` based on the `key` column, completely removing manual ID assignment and `delete()` calls.
- Refactor `BlockSeeder` to use `updateOrInsert` for block schema definitions, removing the truncation of `block_items` and `block_item_property_values`.

### Phase 2: Centralize Locale and Path Logic
- Move the hardcoded locale array `['ru', 'en', 'vi']` into a configuration file or a centralized constant in `BlockContentHelper` (e.g. `BlockContentHelper::getSupportedLocales()`).
- Standardize the `getBlockKeys` and `getBlockContent` methods in `BlockContentHelper` to resolve paths more predictably without relying on deep directory string concatenation in the seeders.

### Phase 3: Abstract Property/Item Seeding
- Create a generic `SeedBlockItemsTask` class or helper method that encapsulates the `upsertItem` -> loop locales -> `upsertPropertyValue` logic. 
- Individual seeders (like `ServicesBlockSeeder` or `BlockForCpDataSeeder`) would then just configure the block key, category, and JSON source path, delegating the actual execution to the generic task.

## 4. Immediate Next Steps

Since we are prioritizing content scaling over architecture rewriting:
1. Do not rewrite the seeders immediately.
2. Ensure new JSON files (like the generated CP offers and service offers) conform strictly to the expected locale fallback logic (using empty objects for unsupported locales like `vi`).
3. If incremental seeding fails due to the destructive nature of `BlocksServicesCategoriesSeeder`, we may need to patch it with `updateOrInsert` to allow safe, repeated `php artisan db:seed` executions.
