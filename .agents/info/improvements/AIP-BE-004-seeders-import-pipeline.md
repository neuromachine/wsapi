# AIP-BE-004 — Seeders and Import Pipeline Refactor

## Status

Draft.

## Type

Architecture Improvement Proposal.

This document is not a task file. It describes a possible direction for improving the backend seed/import layer. It must be reviewed and accepted before being converted into an agent task.

---

## 1. Purpose

The purpose of this proposal is to define a safe improvement path for the backend seeders and block content import pipeline.

The seed/import layer is responsible for turning project content sources into database records used by the Blocks/EAV API system. It is not merely a development convenience. It encodes project content structure, localized content, block keys, category hierarchy, item schemas, and historical decisions.

The target direction is:

```text
same content
same keys
same API compatibility
less duplication
clearer import flow
safer future extension
```

---

## 2. Scope

This AIP concerns the write/import side of the content system:

```text
JSON/content source
  → seeder/helper/import logic
    → blocks/categories/items/properties/values
      → database state
        → later consumed by read-side API
```

It does not directly refactor the API read-side flow. That concern belongs to `AIP-BE-001-read-side-refactor.md`.

---

## 3. Current Understanding

The backend uses a Laravel API with a block-content/EAV model:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

The current project also uses structured seeders and JSON-like content sources. Known elements include:

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
```

The content file pipeline is based around a dedicated `blocks` disk and paths such as:

```text
storage/app/blocks/cat/{category}/{key}.json
```

In Laravel 12 the default `local` disk points to `storage/app/private`, therefore the block content pipeline must use the dedicated `Storage::disk('blocks')` configuration rather than assuming `storage/app` as a general root.

---

## 4. Problem Statement

The current seed/import layer appears functional, but likely contains accumulated operational debt:

```text
- many specialized seeders
- repeated import patterns
- implicit execution order
- mixed legacy and active seeders
- possible hardcoded IDs
- possible hardcoded keys
- multiple content formats
- naming inconsistencies inherited by API output
- weak boundary between content source and DB write logic
- unclear validation of source files
```

This is acceptable in early project development, but it becomes risky as the system grows.

The main risk is not aesthetic. The main risk is that future content expansion becomes fragile and unpredictable.

---

## 5. What Must Not Be Broken

Any seed/import refactor must preserve:

```text
- block keys
- category keys
- item keys
- property keys
- locale coverage
- ru/en/vi data where present
- public API response compatibility
- existing route behavior
- existing frontend expectations
```

The seeders should not silently change meaning of content.

Do not rename content-facing keys such as:

```text
acticle
items
childs
subcategories
locale
```

These may be wrong or legacy, but renaming them requires a compatibility/migration plan.

---

## 6. Desired Direction

The target import pipeline should become easier to reason about:

```text
Content source
  → source validation
    → normalized import structure
      → key-based resolution
        → idempotent DB writes
          → clear logs/errors
```

The primary improvement is not to introduce a large import framework. The primary improvement is to reduce hidden assumptions and repeated code.

---

## 7. Candidate Improvements

### 7.1 Inventory Active vs Legacy Seeders

Before editing, document:

```text
Seeder
Purpose
Input source
Output tables
Dependencies
Likely active / likely legacy / unknown
```

This inventory should prevent accidental deletion or modification of a seeder that still encodes required project behavior.

---

### 7.2 Centralize Source Reading

`BlockContentHelper` or a similar focused helper may be used to centralize:

```text
- checking file existence
- reading JSON
- JSON_THROW_ON_ERROR
- category/file path construction
- meaningful exceptions
```

This should remain a small source-reading utility, not a full importer.

---

### 7.3 Improve Error Messages

Seeder failures should explain:

```text
- missing category directory
- missing JSON file
- invalid JSON
- missing required key
- unknown block key
- unknown category key
- unknown property key
```

A clear failure during seeding is better than a partially seeded database with silent omissions.

---

### 7.4 Prefer Key-Based Resolution Over Hardcoded IDs

Where safe, seeders should resolve entities by stable keys rather than relying on numeric IDs.

Preferred:

```text
Block::where('key', 'descr_data')
BlocksCategories::where('key', 'services')
BlockItemProperty::where('key', 'title')
```

Risk: existing legacy seeders may depend on numeric IDs due to historical ordering. Those places should be identified before changing.

---

### 7.5 Make Idempotency Explicit

Repeated seeding should be predictable.

Acceptable patterns:

```text
updateOrInsert
updateOrCreate
upsert
transaction blocks where needed
```

The chosen strategy should be consistent within a given import path.

---

### 7.6 Preserve Locale and Version Behavior

The value layer contains `locale` and `version`. Seeders must not collapse localized values into one language, remove unknown locales, or ignore version-related fields if they are currently preserved.

Even if `version` is not actively used, it is part of the schema and future draft/publish direction.

---

### 7.7 Avoid Content Mutation During Refactor

A seed/import refactor should not rewrite actual marketing text, HTML, metadata, prices, service names, or portfolio content.

Content cleanup belongs to content tasks, not seeder refactoring.

---

## 8. Proposed Staged Path

### Stage 1 — Inventory

Create a clear map of seeders and import sources.

Output may be a document or task report, not necessarily code.

### Stage 2 — Source Reading Cleanup

Improve or confirm `BlockContentHelper` as the source-reading entry point.

### Stage 3 — Repeated Pattern Extraction

Extract only repeated logic that appears in multiple active seeders.

Avoid abstracting one-off logic.

### Stage 4 — Idempotency and Error Handling

Make writes safer and failures clearer.

### Stage 5 — Regression Against API

After seeding, verify at least:

```text
GET /en/blocks/categories/services
```

The resulting API response should remain compatible with the known response shape.

---

## 9. Recommended Relation to TASK-BE-003

If accepted, this AIP can become:

```text
TASK-BE-003 — Optional Refactor of Block Content Seeders and Import Pipeline
```

That task should be optional and separated from the read-side refactor.

Do not mix seed/import changes with API Resource/Repository refactor in the same agent run unless explicitly requested.

---

## 10. Success Criteria

This improvement direction is successful if:

```text
- seeders are easier to understand
- active and legacy seeders are distinguished
- source file reading is centralized or at least consistent
- error messages become clearer
- repeated logic is reduced
- seeded output remains compatible
- public API response shape is not broken
```

---

## 11. Failure Criteria

This direction fails if:

```text
- content changes unexpectedly
- locales are lost
- keys are renamed without compatibility
- DB output changes unintentionally
- seeders become more abstract and harder to debug
- the refactor becomes a schema redesign
- read-side and seed-side changes are mixed without clear need
```

---

## 12. Open Questions

Before converting this AIP into a task, clarify:

```text
1. Which seeders are still used for current database creation?
2. Which seeders are historical only?
3. Should seeded DB output be compared against wsapi_local_2_07_2026.sql?
4. Should the task allow adding import helper classes?
5. Should the task only inspect first, then stop for approval?
6. Is full reseeding available in the local environment?
```

---

## 13. Current Recommendation

Do not start with a large seeder rewrite.

Start with an inventory and targeted cleanup of file reading, repeated write patterns, and error handling.

Preserve all content-facing keys and localized data.
