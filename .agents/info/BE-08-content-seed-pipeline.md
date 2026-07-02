# BE-08 — Content Seed Pipeline

## Status

Architecture context / backend data import documentation.

This document describes the current and intended role of the block content seeding/import pipeline in the WebSolutions Laravel backend. It is not a task file and does not prescribe immediate code changes.

---

## Purpose

The seed pipeline is responsible for getting structured project content into the database.

The read-side API depends on this seeded data, but the seeding pipeline is a separate concern.

Important separation:

```text
Seed pipeline:
  how data enters the database

Read-side pipeline:
  how data is loaded from the database and returned by API
```

Do not mix refactors of these two systems unless a task explicitly asks for it.

---

## Current Data Model Context

The backend content model is built around:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

Conceptual layers:

```text
STRUCTURE LAYER
  blocks
  blocks_categories

ENTITY LAYER
  block_items

SCHEMA LAYER
  block_item_properties

DATA LAYER
  block_item_property_values
```

The seed pipeline populates these layers.

The API read-side later consumes these layers through Repository, Resource, `EavContentResolver`, and `BlockAttachMap`.

---

## Current File-Based Content Source

The project has a file-based content source under Laravel storage.

Documented structure:

```text
storage/
└── app/
    └── blocks/
        └── cat/
            ├── {category}/
            │   ├── {block_key}.json
            │   └── ...
            └── ...
```

Each JSON file represents structured content for a block/category context.

The filename without `.json` acts as a content key.

---

## Laravel 12 Filesystem Detail

In this project context, Laravel 12 uses a changed `local` disk root:

```text
storage/app/private
```

Therefore block content should not be read through the default `local` disk when targeting:

```text
storage/app/blocks
```

The project uses a dedicated disk:

```php
Storage::disk('blocks')
```

with root:

```text
storage/app/blocks
```

Important rule:

```text
All block content file reads should use Storage::disk('blocks').
```

---

## BlockContentHelper

Current documented helper:

```text
database/seeders/Helpers/BlockContentHelper.php
```

Core methods:

```php
getBlockKeys(string $category): Collection
getBlockContent(string $category, string $key): array
```

Conceptual role:

```text
BlockContentHelper
  → lists JSON files in a content category
  → derives keys from filenames
  → reads JSON content
  → decodes with JSON_THROW_ON_ERROR
```

It is part of the import pipeline, not the runtime API read-side.

---

## Current Import Flow

Conceptual flow:

```text
storage/app/blocks/cat/{category}/{key}.json
    ↓
Storage::disk('blocks')->files('cat/{category}')
    ↓
BlockContentHelper::getBlockKeys($category)
    ↓
BlockContentHelper::getBlockContent($category, $key)
    ↓
Seeder transforms/normalizes array
    ↓
Eloquent write/upsert/updateOrInsert
    ↓
Database tables
```

The exact seeder set may differ by content domain.

Known seeder categories include areas such as:

```text
- blocks
- categories
- items
- properties
- property values
- services
- portfolio
- pages
- navigation
- individual offers
- main sections
```

---

## Why the Seed Pipeline Matters

The API read-side may look complex partly because seeded data contains historical structure, naming debt, and compatibility keys.

Examples of content/schema debt that may originate or be reinforced through seeders:

```text
acticle vs article
childs vs children
items ambiguity
locale-specific property values
hardcoded block keys
hardcoded category keys
legacy JSON shapes
```

However, these should not be “fixed” casually in seeders because the frontend may already depend on the resulting API shape.

---

## Seed Pipeline vs API Contract

Changing seeders can change runtime API output.

For example, changing a property key in seed data may alter frontend-visible response fields after seeding.

Therefore, seeder refactors must preserve:

```text
- content keys
- block keys
- category keys
- locale coverage
- property keys
- property value types
- frontend-facing aliases
```

unless a separate migration task explicitly approves breaking or transitional changes.

---

## Current Architectural Strengths

The file-based seed pipeline has useful properties:

```text
- content can be versioned as files
- content can be reproduced across environments
- JSON parsing can fail loudly with JSON_THROW_ON_ERROR
- Storage disk abstraction isolates Laravel filesystem changes
- seeders encode initial content structure
```

This is a good foundation for a CMS-like or content-platform workflow.

---

## Current Architectural Risks

Known or likely risks:

```text
- many seeders with unclear active/legacy status
- implicit seeder execution order
- possible hardcoded IDs
- repeated logic for resolving blocks/categories/items/properties
- mixed JSON formats
- content keys that double as API contract
- inconsistent naming inherited by frontend
- possible encoding artifacts from exports/imports
```

These risks justify a future optional seeder refactor, but not as part of ordinary read-side cleanup.

---

## Seeder Refactor Boundary

The seed pipeline should be refactored separately from the read-side API.

Acceptable future improvements:

```text
- centralize JSON reading
- improve error messages
- document seeder order
- reduce duplicated resolver logic
- replace hardcoded IDs with key-based lookups where safe
- use transactions where appropriate
- make upsert/updateOrInsert patterns consistent
- preserve output data
```

Forbidden in an ordinary seed refactor:

```text
- change actual content text casually
- rename public keys casually
- remove locale data
- change DB schema
- change API response shape
- rewrite all seeders into a large framework without need
- mix with Resource/Repository refactor
```

---

## Suggested Future Refactor Strategy

When a dedicated seeder task is created, use stages.

### Stage 1 — Inventory

Map each seeder:

```text
Seeder name
Purpose
Input source
Output tables
Dependencies
Active / legacy / unknown
Risk level
```

### Stage 2 — Identify Repetition

Look for repeated operations:

```text
- find block by key
- find category by key
- find property by key
- create/update block item
- create/update property value
- handle locale arrays
- read JSON files
```

### Stage 3 — Extract Small Utilities

Prefer small helpers over large abstraction.

Examples:

```text
- key-based model resolver
- property value writer
- locale payload normalizer
- content file reader
- seeder logging helper
```

### Stage 4 — Preserve Output

After refactor, seeded output should be equivalent unless a difference is explicitly documented and approved.

---

## Relationship to TASK-BE-003

The future optional task `TASK-BE-003` should focus on this pipeline.

It should not be mixed with `TASK-BE-002` read-side refactor.

Expected principle:

```text
same content
same keys
same API compatibility
less duplication
clearer import flow
safer future extension
```

---

## Relationship to Read-Side Refactor

Read-side refactor may reveal that some API weirdness originates in seed data.

However, the default action should be:

```text
Document the seed/source issue.
Preserve compatibility in read-side response.
Move actual seed/content migration to a separate task.
```

This prevents accidental breakage of working frontend pages.

---

## Relationship to AIP Documents

Future Architecture Improvement Proposal documents should discuss:

```text
- whether seed pipeline should become content import layer
- whether JSON source format should be normalized
- whether block metadata should be seeded into DB
- whether naming debt should be solved via alias/migration layer
- how to transition from legacy keys safely
```

Until then, this document only stabilizes understanding.

---

## Guidance for Future Agents

When touching seeders or content import:

```text
1. Inventory before editing.
2. Do not assume old seeders are unused.
3. Preserve keys and locale coverage.
4. Prefer key-based resolution over hardcoded IDs when safe.
5. Do not change API contract accidentally.
6. Do not mix seeder refactor with Resource/Repository refactor.
7. Report content/schema risks separately.
8. Keep generated DB output equivalent where possible.
```

---

## Manual Regression Notes

After seeder changes, verify as much as possible:

```text
- database seeds without fatal errors
- expected blocks exist
- expected categories exist
- expected items exist
- expected properties exist
- expected property values exist for ru/en/vi where applicable
- /en/blocks/categories/services still returns compatible data
- no public keys disappear
```

If full seeding cannot be run, provide a static safety report.

---

## Summary

The content seed pipeline is the write/import side of the backend content system.

Its correct role:

```text
structured source files / seed definitions
  → reproducible database content
```

It should be improved carefully and separately from read-side API refactors.

The goal is not to rewrite content history.

The goal is:

```text
same content
clearer pipeline
less duplication
safer future imports
no broken API consumers
```
