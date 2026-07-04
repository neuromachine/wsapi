# CONTENT-SEEDING-NEXT-STEPS

## Purpose

This document defines the next practical content-production layer after the initial seeding inventory.

The project now has a clearer seed-layer map. The next work should not focus on architecture cleanup unless required for content production.

## Primary direction

```text
JSON content sources
  -> seeders
    -> EAV tables
      -> existing API endpoints
        -> current/future frontend rendering
```

## Current priority

1. Complete English service offers for all service categories.
2. Normalize the quality of service offer packages.
3. Complete English service category descriptions.
4. Generate a batch of individual commercial proposals for the tourism sector in Vietnam.

## Do not do by default

```text
- do not rewrite seeders
- do not change API Resources
- do not change frontend code
- do not rename legacy keys
- do not introduce new DB schema
```

## Current contracts

### Service offers

```text
Source:
  storage/app/blocks/items/{categoryKey}.json

Seeder:
  database/seeders/ServicesBlockSeeder.php

Block key:
  offers

Locale model:
  ru -> root properties
  en -> en.properties
  vi -> vi.properties
```

### Individual commercial proposals

```text
Source:
  storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json

Seeder:
  database/seeders/BlockForCpDataSeeder.php

Block key:
  ind_offers

Endpoint:
  GET /api/{locale}/blocks/categories/offers/{proposalKey}
```
