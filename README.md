# Stage 15 — WS Content Seeding Tasks Package

Purpose: continue practical content production after seeding-layer inventory.

This package focuses on JSON content creation and normalization, not backend architecture refactoring.

Copy `.agents/` into the WSAPI repository root.

Recommended execution order:

```text
CONTENT-001 -> CONTENT-002 -> CONTENT-003 -> CONTENT-004
```

Main rule:

```text
Do not change backend PHP code unless a task explicitly allows it.
Generate and normalize JSON sources according to the current seeding contracts.
```

Key current seeding contracts:

- Service offers source: `storage/app/blocks/items/{categoryKey}.json`
- Service offers seeder: `database/seeders/ServicesBlockSeeder.php`
- Individual CP source: `storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json`
- Individual CP seeder: `database/seeders/BlockForCpDataSeeder.php`
- Locale model: `ru` = root `properties`, `en/vi` = `{locale}.properties`
- Empty locale payloads may exist as placeholders but must not create hollow EAV values.
