# Seeding Layer — Consolidated Context

## Purpose

This document explains how the WS backend content seeding layer currently works for commercial proposals and service offer packages.

The goal is practical content production: generate valid JSON sources, seed them, and expose them through existing API endpoints without changing frontend logic or API shape.

---

## Two related but different content types

### 1. Service offers

Used for offer/package blocks inside regular service category pages.

```text
Content source:
  storage/app/blocks/items/{service-category-key}.json

Seeder:
  database/seeders/ServicesBlockSeeder.php

Block key:
  offers

Category:
  concrete service category key, e.g.
  posadocnaia-stranica
  internet-katalog
```

Current locale model:

```text
ru:
  root properties

en / vi:
  {locale}.properties
```

`ServicesBlockSeeder` now iterates through `ru`, `en`, `vi`. It reads root `properties` for `ru` and `$itemDef[$locale]['properties']` for non-RU. Empty property objects are bypassed and must not create hollow EAV values.

---

### 2. Individual commercial proposals / КП

Used for complex standalone commercial proposal pages and advanced offer rendering.

```text
Content source:
  storage/app/blocks/blocks/items/ind_offers/{proposal-key}.json

Category descriptor:
  storage/app/blocks/cat/ind_offers.json

Seeder:
  database/seeders/BlockForCpDataSeeder.php

Block key:
  ind_offers

API endpoint:
  GET /api/{locale}/blocks/categories/offers/{proposal-key}
```

Current locale model:

```text
ru:
  root properties

en / vi:
  {locale}.properties
```

`BlockForCpDataSeeder` loops through `['ru', 'en', 'vi']`. For `ru`, it reads root `properties`; for non-RU, it reads `$data[$section]['properties'] ?? []`.

---

## Active vs legacy seeders

Active for individual CP:

```text
BlockForCpDataSeeder
```

Legacy / do not use for new CP production:

```text
KpBlockSeeder
```

`KpBlockSeeder` reflects an older strategy and should be treated only as reference material unless explicitly reactivated.

---

## Important schema gap

The conceptual CP model and older examples may contain:

```text
final
```

But the current `BlockForCpDataSeeder` property registry for block `ind_offers` includes only:

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

Therefore, `final` is not canonical unless a backend/schema task adds it to `block_item_properties` for `ind_offers`.

For now:

```text
- use acticle for the final persuasive closing statement / summary;
- do not rely on final being seeded;
- do not add arbitrary property keys in JSON unless the seeder property registry supports them.
```

---

## Production rule

When creating content, do not change backend code unless the task explicitly asks for a seeder/schema extension.

The normal content path is:

```text
Generate JSON file
  -> place in storage/app/blocks/blocks/items/ind_offers/
    -> run php artisan db:seed --class=BlockForCpDataSeeder
      -> verify /api/{locale}/blocks/categories/offers/{key}
```

