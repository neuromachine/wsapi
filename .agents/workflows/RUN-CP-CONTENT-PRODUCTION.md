# Workflow — CP Content Production

## Purpose

Generate, seed, and verify individual commercial proposal content for WS.

This workflow is content-first. Do not refactor backend or frontend unless explicitly requested.

---

## Phase 1 — Prepare input

Human operator prepares batch input using:

```text
.agents/templates/cp_batch_input.template.md
```

Required:

```text
segment
geography
target audience
languages
entries[]
```

---

## Phase 2 — Generate JSON files

Run:

```text
.agents/tasks/TASK-CP-001-batch-generate-ind-offers.md
```

Expected output:

```text
storage/app/blocks/blocks/items/ind_offers/{key}.json
```

---

## Phase 3 — Seed and verify

Run:

```text
php artisan db:seed --class=BlockForCpDataSeeder
```

Then verify:

```text
GET /api/ru/blocks/categories/offers/{key}
GET /api/en/blocks/categories/offers/{key}
```

---

## Phase 4 — Report

Create:

```text
.agents/reports/REPORT-CP-001-batch-generate-ind-offers.md
.agents/reports/REPORT-CP-002-seed-and-verify-generated-ind-offers.md
```

