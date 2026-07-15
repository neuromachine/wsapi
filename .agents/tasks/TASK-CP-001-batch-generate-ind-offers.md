# TASK-CP-001 — Batch Generate Individual Commercial Proposals

## Type

Content production / JSON generation.

## Goal

Generate a batch of valid `ind_offers` commercial proposal JSON files from a human-provided segment and list of entries.

## Inputs

The human operator must provide or paste a batch brief using:

```text
.agents/templates/cp_batch_input.template.md
```

## Required context

Read:

```text
.agents/contracts/IND-OFFERS-CP-CONTENT-CONTRACT.md
.agents/contracts/CP-CONTENT-MODEL.md
.agents/prompts/CP_BATCH_GENERATION_PROMPT.md
```

Inspect examples if present:

```text
storage/app/blocks/blocks/items/ind_offers/visarun_system.json
storage/app/blocks/blocks/items/ind_offers/default.json
storage/app/blocks/blocks/items/ind_offers/medical_platform_en.json
```

## Output location

Create one file per entry:

```text
storage/app/blocks/blocks/items/ind_offers/{key}.json
```

## Constraints

Do not modify:

```text
app/**
database/seeders/**
routes/**
frontend files
```

Do not invent unsupported property keys. Current supported keys are:

```text
title
content
acticle
hero
benefits
extras
important
items
includes
reelsSystem
```

Use `acticle` for the closing summary. Do not use `final` unless a schema task adds it first.

## Content expectations

For each CP:

```text
hero: strong market/client pain
benefits: 4 business values
extras: 3–5 measurable/practical effects
important: functional blocks if system-level solution
items: 3 packages with realistic price/term/features
includes: 3–5 shared baseline values
```

## Locale behavior

```text
ru/default: required in root properties
en: required only if brief asks for English
vi: full content only if brief asks; otherwise placeholder allowed
```

## Validation

Before completion:

```text
- run JSON parse/lint if possible
- ensure file name matches root key
- ensure block = ind_offers
- ensure no unsupported property keys
```

## Report

Create:

```text
.agents/reports/REPORT-CP-001-batch-generate-ind-offers.md
```

Include:

```text
- entries generated
- files created
- locale coverage
- deviations / placeholders
- unsupported fields intentionally avoided
```

