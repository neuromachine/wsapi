# SEEDING-CONTEXT-MAP — Current WS Content Seeding Map

## Purpose

This document explains the real current seeding/import landscape before adding new WS package/calculator data.

The project currently has several content types stored as JSON files and imported into the EAV tables through Laravel seeders.

## Core EAV tables

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

## Core helpers

```text
database/seeders/Helpers/BlockContentHelper.php
database/seeders/Helpers/ImportHelper.php
```

### ImportHelper role

`ImportHelper` is the stable write helper:

```text
upsertBlock()
getBlockId()
getCategoryId()
upsertProperty()
getPropertyId()
upsertItem()
upsertPropertyValue()
```

It should be preferred over raw repeated DB writes when practical.

### BlockContentHelper role

`BlockContentHelper` is the file-reading bridge.

Important patterns:

```text
getBlockKeys($path, 'category')
  reads storage/app/blocks/cat/{path}/*.json

getBlockKeys($path, 'block' | 'cp' | other non-category type)
  reads storage/app/blocks/{path}/*.json directly

getBlockContent($path, $key, 'category')
  reads storage/app/blocks/cat/{path}/{key}.json

getBlockContent($path, $key, non-category type)
  reads storage/app/blocks/{path}/{key}.json

getCategoryItemsData($categoryKey)
  reads storage/app/blocks/items/{categoryKey}.json
```

## Active DatabaseSeeder order

Current active flow:

```text
BlocksMainCategoriesSeeder
BlocksServicesCategoriesSeeder
BlocksCategoriesSeeder
BlockSeeder
BlockItemsForCategoriesDesrDataSeeder
BlockForPagesDataSeeder
BlockForPortfolioDataSeeder
BlockForCpDataSeeder
BlockItemPropertiesSeeder
ServicesBlockSeeder
BlocksForMainSectionsSeeder
BlocksForNavigationSeeder
```

Legacy/commented:

```text
BlockItemsSeeder
BlockItemPropertyValuesSeeder
BlocksForPortfolioPropertyValuesSeeder
KpBlockSeeder
```

## Content families

### 1. Categories

Sources:

```text
storage/app/blocks/categories.json
storage/app/blocks/cat/*.json
storage/app/blocks/cat/{group}/*.json
```

Used for category tree and category descriptions.

### 2. descr_data

Sources:

```text
storage/app/blocks/blocks/items/descr_data/*.json
```

Used to attach page/category textual content through the `descr_data` block.

### 3. service offer packages

Current source family:

```text
storage/app/blocks/items/{categoryKey}.json
```

Current seeder:

```text
ServicesBlockSeeder
```

Current issue:

```text
ServicesBlockSeeder writes only locale 'ru'.
```

This is the immediate target of `TASK-SEED-001`.

### 4. individual commercial offers / CP

Current source family:

```text
storage/app/blocks/blocks/items/ind_offers/*.json
```

Current seeder:

```text
BlockForCpDataSeeder
```

Current properties:

```text
title
content
acticle
items
hero
benefits
includes
reelsSystem
extras
important
```

Current issue:

```text
Some CP files have root `properties` only.
Some have only `en.properties` or `vi.properties`.
The category-level `cat/ind_offers.json` file may be missing.
```

This is the immediate target of `TASK-SEED-002`.

### 5. navigation

Sources:

```text
storage/app/blocks/blocks/navigation.json
storage/app/blocks/blocks/items/navigation/*.json
```

Navigation item files use explicit `scope` per file.

### 6. pages/main/portfolio

These are separate content families and should not be mixed into the offers/package tasks unless required.

## Current product direction

The next business feature is not “more architecture cleanup”.

The next business direction is:

```text
Offers / packages
  → service package data
  → calculator items and prices
  → simple order payload
  → dynamic commercial proposal preview
```

Therefore seeding tasks should prepare data that the frontend calculator can consume later.

