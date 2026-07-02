# TASK-BE-003 — Optional Refactor of Block Content Seeders and Import Pipeline

## Status

Optional / follow-up backend refactoring task.

## Priority

Medium.

## Relation to TASK-BE-002

This task should be executed only after or independently from the read-side refactor.

It must not be mixed into TASK-BE-002.

TASK-BE-002 improves how data is read and returned by API.
TASK-BE-003 improves how block/content data is seeded/imported into the database.

---

## Core Principle

Improve without breaking.

The seeders and import pipeline may be messy or repetitive, but they contain working project knowledge. Refactor only to make the system clearer, safer, and easier to maintain.

Do not casually change actual content, keys, locale data, or generated database output.

---

## Context

The project uses seeded content and block data for a Laravel API backend.

The content model includes:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

Seed/import sources may include structured JSON files and multiple seeders for:

```text
blocks
categories
items
properties
property values
services
portfolio
pages
navigation
individual offers
main sections
```

There is also a known file-based content pipeline using block JSON files and helper logic.

The current system appears functional but may have accumulated duplication, naming debt, implicit assumptions, and fragile ordering.

---

## Main Goal

Refactor the seeding/import pipeline so that it becomes more understandable, safer, more idempotent, and easier to extend, while preserving the resulting seeded data as much as possible.

The priority is not to redesign the whole content architecture.
The priority is to reduce operational fragility.

---

## Primary Target Area

Inspect relevant files such as:

```text
database/seeders/DatabaseSeeder.php
database/seeders/BlockSeeder.php
database/seeders/BlocksCategoriesSeeder.php
database/seeders/BlockItemPropertiesSeeder.php
database/seeders/BlockItemsSeeder.php
database/seeders/BlockItemPropertyValuesSeeder.php
database/seeders/BlocksServicesCategoriesSeeder.php
database/seeders/ServicesBlockSeeder.php
database/seeders/KpBlockSeeder.php
database/seeders/BlocksForNavigationSeeder.php
database/seeders/BlocksForMainSectionsSeeder.php
database/seeders/BlockForPagesDataSeeder.php
database/seeders/BlockForPortfolioDataSeeder.php
database/seeders/Helpers/BlockContentHelper.php
config/filesystems.php
storage/app/blocks/**
```

Exact file list may differ in the working tree. Inspect before editing.

---

## What Must Be Investigated

Before editing, determine:

```text
1. Which seeders are currently active.
2. Which seeders are legacy.
3. Which seeders depend on execution order.
4. Which seeders create blocks/categories/items/properties/values.
5. Which seeders read JSON files.
6. Which seeders duplicate logic.
7. Which seeders rely on hardcoded IDs.
8. Which seeders rely on hardcoded keys.
9. Which seeders are safe to refactor.
10. Which content keys must remain untouched.
```

Do not assume that a seeder is unused only because it looks old.

---

## Desired Direction

Move toward a clearer import pipeline:

```text
Content source
  → validation / normalization
    → block/category/item/property resolution
      → idempotent DB write
        → clear logs / errors
```

The exact implementation is open, but the output must remain compatible.

---

## Acceptable Improvements

The agent may:

```text
- remove duplicated helper logic
- centralize JSON reading
- improve error messages for missing files or invalid JSON
- improve transaction boundaries
- make upsert/updateOrInsert usage more consistent
- reduce hardcoded IDs where keys can be used safely
- add small helper methods/classes if justified
- document seeder ordering
- add dry-run style comments or manual run notes
- normalize repeated import loops
- make failures more explicit
```

---

## Forbidden Changes

Do not:

```text
- change database schema
- change public API response shape
- change actual text/content unless explicitly asked
- rename content keys such as acticle/items/childs in this task
- delete seeders without proving they are unused
- change locale coverage
- remove ru/en/vi data
- rewrite all seeders into a large new framework
- introduce external packages
- touch frontend code
- mix this task with Resource/Repository refactor
```

---

## Known Naming and Content Debt

The agent may document but should not automatically fix:

```text
acticle vs article
items.items ambiguity
childs vs children
locale vs section/scope
encoding artifacts in exported data
legacy seeders
duplicated JSON formats
```

These may become future migration tasks.

For this task, preserve compatibility.

---

## Suggested Refactor Strategy

### Stage 1 — Inventory

Map the current seeders:

```text
Seeder name
Purpose
Input source
Output tables
Dependencies
Potential legacy status
```

### Stage 2 — Identify Safe Consolidation

Look for repeated patterns:

```text
- reading block JSON
- resolving block by key
- resolving category by key
- resolving property by key
- creating/updating item
- writing property values by locale
```

### Stage 3 — Improve Smallest Useful Layer

Prefer small helpers over a full rewrite.

Examples:

```text
BlockContentHelper improvements
Seeder utility methods
Shared resolver methods
Consistent upsert helpers
Clear validation exceptions
```

### Stage 4 — Preserve Output

The result of running seeders should be equivalent unless a difference is intentionally documented and justified.

---

## Manual Regression Checklist

After changes, verify as much as possible:

```text
- database can be seeded without fatal errors
- blocks are created
- categories are created
- block_items are created
- block_item_properties are created
- block_item_property_values are created
- localized values remain present
- JSON fields remain decoded correctly by API
- /en/blocks/categories/services still returns expected data
- existing content keys remain available
```

If full seeding cannot be run, provide a static safety report.

---

## Expected Deliverables

The final agent report must include:

```text
1. Files inspected.
2. Files changed.
3. Which seeders are active vs likely legacy.
4. What duplication was reduced.
5. What behavior was intentionally preserved.
6. Any content/schema risks discovered.
7. Manual regression result or reason it could not be run.
8. Recommended next task.
```

---

## Success Criteria

This task is successful if:

```text
- seeder/import code becomes easier to understand
- repeated logic is reduced
- errors become clearer
- content output is preserved
- no public API contract is broken
- future content additions become safer
```

---

## Failure Criteria

This task fails if:

```text
- seeded content changes unexpectedly
- locale data is lost
- keys are renamed without migration
- API output changes unexpectedly
- seeders become more abstract and harder to follow
- the task turns into a database redesign
```

---

## Core Reminder

The seeders are not just setup scripts.
They encode project content structure and migration history.

Refactor them carefully.

The goal:

```text
same content
same keys
same API compatibility
less duplication
clearer import flow
safer future extension
```
