# Stage 14 — CP / Commercial Proposal Seeding Method Package

This package consolidates the seeding/content layer for WS commercial proposals (`ind_offers`) and service offer packages (`offers`).

It is intended for agent-assisted content production, not backend refactoring.

## Current goal

Prepare reusable agent materials so an agent can generate and normalize commercial proposal JSON files for:

```text
storage/app/blocks/blocks/items/ind_offers/*.json
```

and keep them compatible with:

```text
php artisan db:seed --class=BlockForCpDataSeeder
GET /api/{locale}/blocks/categories/offers/{slug}
```

## Contents

```text
.agents/
  info/
    SEEDING-LAYER-CONSOLIDATED-CONTEXT.md

  contracts/
    IND-OFFERS-CP-CONTENT-CONTRACT.md
    SERVICE-OFFERS-CONTENT-CONTRACT.md
    CP-CONTENT-MODEL.md

  templates/
    ind_offer_json.template.json
    cp_batch_input.template.md

  prompts/
    CP_BATCH_GENERATION_PROMPT.md

  workflows/
    RUN-CP-CONTENT-PRODUCTION.md

  tasks/
    TASK-CP-001-batch-generate-ind-offers.md
    TASK-CP-002-seed-and-verify-generated-ind-offers.md
    LAUNCH-TASK-CP-001.md
    LAUNCH-TASK-CP-002.md

  reports/templates/
    REPORT-CP-001-batch-generate-ind-offers.template.md
    REPORT-CP-002-seed-and-verify-generated-ind-offers.template.md
```

## Recommended first run

Use:

```powershell
Get-Content .agents\tasks\LAUNCH-TASK-CP-001.md -Raw | Set-Clipboard
```

Paste into the agent and provide a batch input using `.agents/templates/cp_batch_input.template.md`.

