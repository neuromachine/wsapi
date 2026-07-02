# Skill: WS Backend Seed Pipeline

## Purpose

Use this skill when working with seeders, JSON content import, block content initialization, or any task related to how content enters the database.

This skill is not for read-side API response refactoring. Keep seed/import work separate from Resource/Repository response work unless the task explicitly connects them.

---

## Core Rule

Seeders encode project history and content structure.

Refactor them carefully.

```text
Same content.
Same keys.
Same locales.
Same API compatibility.
Less duplication.
Clearer import flow.
Safer future extension.
```

---

## Current Project Context

The project uses a block/content system based on:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

Known import-related elements include:

```text
database/seeders/*
database/seeders/Helpers/BlockContentHelper.php
storage/app/blocks/**
config/filesystems.php
```

Known content source pattern:

```text
storage/app/blocks/cat/{category}/{key}.json
```

Laravel 12 local disk behavior is relevant. The project uses a dedicated `blocks` disk for block JSON files. Do not casually switch it back to the default `local` disk.

---

## Separate Read-Side From Seed-Side

Do not mix these concerns:

```text
Read-side:
  Repository → Resource → JSON response

Seed-side:
  JSON/source data → Seeder/helper → DB tables
```

A read-side refactor should not rewrite seeders.
A seeder refactor should not change API Resources unless explicitly requested.

---

## What to Inspect Before Editing

Before changing seeders, inspect:

```text
- DatabaseSeeder execution order
- which seeders are currently called
- which seeders appear legacy
- JSON file locations and naming conventions
- dependency order: blocks → categories → properties → items → values
- hardcoded IDs
- hardcoded keys
- locale handling
- updateOrInsert / upsert behavior
- transaction usage
- error handling for missing files or invalid JSON
```

Do not assume an old-looking seeder is unused.

---

## Acceptable Improvements

Allowed when justified:

```text
- reduce duplicated JSON reading logic
- improve BlockContentHelper error messages
- centralize repeated key resolution
- make upsert/updateOrInsert behavior consistent
- improve transaction boundaries
- replace hardcoded IDs with key lookup where safe
- add small helper methods/classes
- document seeder order
- make invalid JSON / missing file failures clearer
```

---

## Forbidden Changes

Do not:

```text
- change DB schema
- change actual content text unless requested
- rename public content keys
- remove ru/en/vi data
- delete seeders without proving they are unused
- change API response shape
- introduce external packages
- mix seed refactor with frontend work
- rewrite everything into a new import framework
```

---

## Known Compatibility Debt

Document but do not automatically fix:

```text
acticle vs article
childs vs children
items ambiguity
locale vs scope/section
legacy JSON source paths
possible encoding artifacts in exported data
```

These require separate migration/compatibility decisions.

---

## Safe Refactor Strategy

Use staged refactoring:

```text
1. Inventory active seeders.
2. Identify repeated import patterns.
3. Improve the smallest shared layer.
4. Preserve DB output.
5. Verify API still reads expected data.
```

Prefer improving helper logic over broad seeder rewrites.

---

## Manual Regression

If a seeder/import task is executed, verify as much as possible:

```text
- seed command runs or static safety is reported
- blocks exist
- categories exist
- block_items exist
- properties exist
- property values exist
- locale values remain available
- JSON values decode correctly in API
- /en/blocks/categories/services remains compatible
```

If full seeding cannot be run, state exactly why and provide a static review.

---

## Final Report Requirements

Report:

```text
- files inspected
- files changed
- active vs likely legacy seeders
- duplication reduced
- output compatibility preserved
- known risks
- regression performed or blocked
- recommended next task
```
