# CONTRACT — Content Seeder Contract

## Purpose

Rules for choosing seeders and understanding what each seeder is allowed to update.

---

## 1. Normal content-production seeders

### `ServicesBlockSeeder`

```yaml
source: storage/app/blocks/items/{categoryKey}.json
target_block: offers
main_tables:
  - block_items
  - block_item_properties
  - block_item_property_values
locale_model:
  ru: root properties
  en: en.properties
  vi: vi.properties
safe_to_rerun_for_content: true
run_command: php artisan db:seed --class=ServicesBlockSeeder
```

### `BlockForCpDataSeeder`

```yaml
source: storage/app/blocks/blocks/items/ind_offers/*.json
target_block: ind_offers
main_tables:
  - blocks
  - block_items
  - block_item_properties
  - block_item_property_values
locale_model:
  ru: root properties
  en: en.properties
  vi: vi.properties
safe_to_rerun_for_content: true
run_command: php artisan db:seed --class=BlockForCpDataSeeder
```

---

## 2. Conditional content seeders

These may be used when the task explicitly targets their content family:

```text
BlockForPagesDataSeeder
BlockForPortfolioDataSeeder
BlockItemsForCategoriesDesrDataSeeder
BlocksForMainSectionsSeeder
BlocksForNavigationSeeder
```

Before running:

```text
- confirm the target JSON family;
- confirm the seeder is idempotent enough for the local state;
- inspect diff and run targeted API checks.
```

---

## 3. Structural seeders

Treat as dangerous by default:

```text
BlocksMainCategoriesSeeder
BlocksServicesCategoriesSeeder
BlocksCategoriesSeeder
BlockSeeder
```

Risks:

```text
- manual ID behavior;
- delete/truncate behavior;
- foreign key relation disruption;
- broad rebuild of block schemas/categories.
```

Do not run them during normal JSON content generation unless the task explicitly requires structural rebuild.

---

## 4. Seeder output rule

A seeder should resolve and write the following chain:

```text
block key -> block_id
category key -> category_id
item key -> item_id
property key -> property_id
locale -> property value row
```

Agents should prefer this stable key-based chain over hardcoded numeric IDs.

---

## 5. Report requirements after seeding tasks

Every content seeding task should report:

```text
- source files changed;
- target seeder;
- target block key;
- locales changed;
- affected endpoints;
- seed command;
- manual verification steps;
- any human review decisions left open.
```
