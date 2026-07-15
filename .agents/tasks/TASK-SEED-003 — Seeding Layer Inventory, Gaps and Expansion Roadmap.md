# TASK-SEED-003 — Seeding Layer Inventory, Gaps and Expansion Roadmap

## Status

Analytical / architecture consolidation task.

## Priority

High.

## Goal

Study the current WSAPI seeding layer and produce a practical map of how all non-CP content types are seeded, where the gaps are, what is duplicated, what is legacy, and what should be improved before further large-scale content generation.

This task must use the agent’s full codebase access.

The output should help the project move from “individual seeder knowledge” to a reusable content-seeding architecture.

---

## Context

The project uses Laravel seeders to populate a dynamic block/content system based on:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

Current known content flows include:

```text
main categories
service categories
category description items
pages
portfolio
individual commercial proposals / ind_offers
service offer packages
main page sections
navigation
legacy block/item/property seeders
```

Recent work already clarified two important areas:

```text
TASK-SEED-001
  Service offers locale expansion.
  ServicesBlockSeeder now supports ru/en/vi and skips empty locale property payloads.

TASK-SEED-002
  ind_offers normalization.
  BlockForCpDataSeeder is the canonical path for individual commercial proposals.
```

This task must now inspect the broader seeding layer beyond those two cases.

---

## Primary Files to Inspect

Inspect at minimum:

```text
database/seeders/DatabaseSeeder.php

database/seeders/BlockSeeder.php
database/seeders/BlocksMainCategoriesSeeder.php
database/seeders/BlocksServicesCategoriesSeeder.php
database/seeders/BlocksCategoriesSeeder.php
database/seeders/BlockItemsForCategoriesDesrDataSeeder.php
database/seeders/BlockForPagesDataSeeder.php
database/seeders/BlockForPortfolioDataSeeder.php
database/seeders/BlockForCpDataSeeder.php
database/seeders/ServicesBlockSeeder.php
database/seeders/BlocksForMainSectionsSeeder.php
database/seeders/BlocksForNavigationSeeder.php
database/seeders/BlockItemPropertiesSeeder.php

database/seeders/Helpers/BlockContentHelper.php
database/seeders/Helpers/ImportHelper.php

database/seeders/KpBlockSeeder.php
database/seeders/BlockItemsSeeder.php
database/seeders/BlockItemPropertyValuesSeeder.php
database/seeders/BlocksForPortfolioPropertyValuesSeeder.php

config/filesystems.php

storage/app/blocks/**
```

Also inspect the content source package / packed content representation if present in the repository context.

---

## Scope

This is primarily a read-only analysis task.

Allowed changes:

```text
.agents/reports/REPORT-SEED-003-seeding-layer-inventory-and-roadmap.md
.agents/info/SEEDING-LAYER-INVENTORY.md
```

Do not modify production seeders, JSON content, models, migrations, routes, frontend code, or API resources in this task.

---

## Questions to Answer

### 1. Seeder execution order

Map the actual execution order from `DatabaseSeeder`.

For each active seeder, document:

```text
Seeder name
Purpose
Input source
Output tables
Depends on
Run order sensitivity
Idempotency strategy
Locale behavior
Current status
```

Mark each seeder as:

```text
active
active but fragile
active but partially legacy
legacy / disabled
unclear
```

---

### 2. Content type registry

Build a practical registry of content types currently supported by seeders.

At minimum classify:

```text
main categories
service categories
service category descriptions
service offer packages
individual commercial proposals / ind_offers
pages
portfolio works
main page sections
navigation
block schema / properties
legacy generic items
```

For each type, identify:

```text
source path
expected JSON shape
seeder responsible
public endpoint affected
frontend consumption area if obvious
locale support
known gaps
```

---

### 3. Source path map

Create a map of source directories and what they mean.

Examples to verify:

```text
storage/app/blocks/categories.json
storage/app/blocks/cat/*.json
storage/app/blocks/cat/{category}/*.json
storage/app/blocks/items/*.json
storage/app/blocks/blocks/items/ind_offers/*.json
storage/app/blocks/blocks/items/navigation/*.json
storage/app/blocks/blocks/items/pages/*.json
storage/app/blocks/blocks/items/descr_data/*.json
```

For each path, answer:

```text
Who reads it?
What structure is expected?
Is it canonical or legacy?
Is locale supported?
Is it flat or nested?
```

---

### 4. Locale coverage

Assess how each content type handles locales.

Classify as:

```text
ru only
ru/en/vi supported
partial en
hollow vi
not localized
unknown
```

Specifically check whether each seeder:

```text
hardcodes ru
loops through sections
uses root properties for ru
uses {locale}.properties for en/vi
skips empty locale payloads
creates empty EAV values accidentally
```

---

### 5. JSON format consistency

Compare JSON shapes across content types.

Look for:

```text
root properties object
localized en.properties / vi.properties
category-level localized descriptions
items arrays
nested blocks
legacy keys such as acticle, childs, section
duplicated items.items structures
missing required fields
inconsistent icon/index/sort fields
```

Do not rename anything. Only document.

---

### 6. Seeder implementation quality

Inspect for:

```text
duplicated DB logic
hardcoded IDs
hardcoded block keys
hardcoded category keys
manual property resolution
raw insert vs updateOrInsert/upsert
transaction usage
foreign key toggling
weak error messages
silent skips
legacy dead code
non-idempotent behavior
```

Separate findings into:

```text
safe to improve soon
should only document
dangerous to touch now
```

---

### 7. API/frontend impact

For each seeding flow, identify which API endpoints are likely affected.

Examples:

```text
GET /api/{locale}/blocks/categories/{slug}
GET /api/{locale}/blocks/categories/offers/{slug}
GET /api/{locale}/blocks/items/{slug}
GET /api/{locale}/blocks/categories/structure/{slug?}
```

Do not change endpoint shape.

The goal is to understand which frontend areas would be affected by seed changes.

---

## Required Output

Create:

```text
.agents/reports/REPORT-SEED-003-seeding-layer-inventory-and-roadmap.md
```

The report must include:

```text
1. Executive summary
2. DatabaseSeeder execution map
3. Seeder-by-seeder table
4. Content type registry
5. Source path map
6. Locale coverage matrix
7. JSON shape inconsistencies
8. Legacy / disabled seeder analysis
9. Main risks and omissions
10. Optimization proposals
11. Suggested next practical tasks
```

Also create:

```text
.agents/info/SEEDING-LAYER-INVENTORY.md
```

This must be shorter and reusable by future agents. It should describe the canonical content types and which seeder/source path handles each one.

---

## Expected Recommendations

At the end, propose 5–10 next tasks, grouped by type:

```text
content normalization
locale expansion
seeder cleanup
validation tooling
frontend/data contract support
```

Examples of possible tasks:

```text
TASK-SEED-004 — Normalize Pages Content Locale Structure
TASK-SEED-005 — Portfolio Data Seeding Contract
TASK-SEED-006 — Navigation Seeding Contract and Locale Audit
TASK-SEED-007 — Main Page Sections Content Contract
TASK-SEED-008 — Seeder Helper Consolidation Proposal
TASK-SEED-009 — JSON Content Validation Command
TASK-SEED-010 — Legacy Seeder Retirement Plan
```

Do not implement those tasks yet. Only propose them with short descriptions.

---

## Constraints

Do not:

```text
- modify production seeders
- modify JSON content
- rename legacy keys
- change database schema
- change frontend code
- change API resources
- introduce new packages
- convert the system into a new architecture
```

The purpose is not to make the seed layer theoretically perfect.

The purpose is to understand:

```text
what exists
what feeds what
what is missing
what is risky
what should be normalized next
```

---

## Final Note

Keep the analysis practical.

This project intentionally contains historical layers. Do not treat every inconsistency as a bug. Distinguish between:

```text
intentional legacy compatibility
temporary content debt
real operational risk
future optimization opportunity
```
