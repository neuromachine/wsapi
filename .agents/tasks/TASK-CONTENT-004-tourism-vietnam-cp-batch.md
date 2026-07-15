# TASK-CONTENT-004 — CP Batch Generation for Tourism / Vietnam

## Status

Practical commercial proposal generation task.

## Priority

Medium / after service offer coverage.

## Goal

Generate a batch of individual commercial proposal JSON files for the tourism sector in Vietnam.

These files should become seedable through:

```text
database/seeders/BlockForCpDataSeeder.php
```

## Target path

```text
storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json
```

## Contract

Follow:

```text
.agents/contracts/IND-OFFERS-TOURISM-CP-BATCH-CONTRACT.md
```

## Input

Use or request a filled batch input based on:

```text
.agents/templates/cp_tourism_batch_input.template.md
```

## Required minimum batch

Unless user provides another list, generate proposals for:

```text
visa-run agency
tour desk / excursion seller
transfer service
hotel / apartment rental
beauty services for tourists
medical tourism clinic
```

## Content requirements

Each proposal should include:

```text
hero
benefits
extras
important
items
includes
acticle
```

Package block should include 3 packages:

```text
Entry / Growth / System
```

## Forbidden

Do not:

```text
- modify BlockForCpDataSeeder
- add unsupported properties like final
- change API contract
- change frontend code
```

## Validation

After file creation, recommend:

```bash
php artisan db:seed --class=BlockForCpDataSeeder
```

Then inspect:

```text
GET /api/en/blocks/categories/offers/{proposalKey}
```

## Required report

Create:

```text
.agents/reports/REPORT-CONTENT-004-tourism-vietnam-cp-batch.md
```

Report must include:

```text
- generated proposal keys
- output files
- language coverage
- pricing assumptions
- unresolved human decisions
```
