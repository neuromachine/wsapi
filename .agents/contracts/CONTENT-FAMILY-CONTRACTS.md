# CONTRACT — Content Family Contracts

## Purpose

Single reference for how each major content family is authored, seeded, and exposed.

---

## 1. Service offers

```yaml
family: service_offers
source: storage/app/blocks/items/{categoryKey}.json
seeder: ServicesBlockSeeder
block_key: offers
locales:
  ru: root properties
  en: en.properties
  vi: vi.properties placeholder allowed
safe_seed_command: php artisan db:seed --class=ServicesBlockSeeder
verify_endpoint: GET /api/en/blocks/categories/{categoryKey}
```

Rules:

```text
- preserve package count;
- translate/adapt within en.properties;
- do not change prices unless business task requires;
- one featured package per package set;
- middle/business tier is preferred featured;
- remove duplicate features;
- keep vi empty if not part of task.
```

---

## 2. Service category descriptions

```yaml
family: service_category_descriptions
source: storage/app/blocks/cat/*.json
fields:
  - en.properties.descr
  - en.properties.content
verify_endpoint: GET /api/en/blocks/categories/services
```

Rules:

```text
- preserve tree/category structure;
- preserve legacy keys like childs;
- EN baseline should say what the service is, who it is for, and business result;
- _blank.json is template/hollow.
```

---

## 3. Individual commercial proposals / CP

```yaml
family: ind_offers
source: storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json
seeder: BlockForCpDataSeeder
block_key: ind_offers
endpoint: GET /api/{locale}/blocks/categories/offers/{proposalKey}
locales:
  ru: root properties
  en: en.properties
  vi: vi.properties placeholder allowed
safe_seed_command: php artisan db:seed --class=BlockForCpDataSeeder
```

Canonical sections:

```text
hero
benefits
extras
important
items
includes
acticle
```

Optional/currently supported:

```text
content
reelsSystem
title
```

Unsupported unless schema is extended:

```text
final
```

---

## 4. Pages

```yaml
family: pages
source: storage/app/blocks/blocks/items/pages/*.json
seeder: BlockForPagesDataSeeder
block_key: pages
status: documented, not recently normalized
```

Future need:

```text
TASK-CONTENT-PAGES-001 — Pages JSON contract and EN/VI audit
```

---

## 5. Portfolio

```yaml
family: portfolio
source: storage/app/blocks/cat/portfolio/*.json and related portfolio item data
seeder: BlockForPortfolioDataSeeder
block_key: portfolio
status: documented, not recently normalized
```

Future need:

```text
TASK-CONTENT-PORTFOLIO-001 — Portfolio content contract and project field audit
```

---

## 6. Navigation

```yaml
family: navigation
source: storage/app/blocks/blocks/items/navigation/*.json
seeder: BlocksForNavigationSeeder
block_key: navigation
locale_model: scope/file-based, not always root/en/vi properties
status: separate contract required
```

Future need:

```text
TASK-CONTENT-NAV-001 — Navigation JSON/source/scope contract
```

---

## 7. Main page sections

```yaml
family: main_sections
source: storage/app/blocks/cat/main/*.json
seeder: BlocksForMainSectionsSeeder
status: separate UI-contract required
```

Future need:

```text
TASK-CONTENT-MAIN-001 — Home page section contract and frontend mapping
```
