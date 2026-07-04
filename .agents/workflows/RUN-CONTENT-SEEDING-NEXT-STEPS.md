# RUN-CONTENT-SEEDING-NEXT-STEPS

## Goal

Execute the next practical content-seeding sequence after SEED-003 inventory.

This workflow focuses on JSON content creation and normalization.

## Execution order

```text
TASK-CONTENT-001 — Complete EN Service Offers for All Service Categories
TASK-CONTENT-002 — Service Offers Package Quality Normalization
TASK-CONTENT-003 — EN Category Descriptions for Service Catalog
TASK-CONTENT-004 — CP Batch Generation for Tourism / Vietnam
```

## Global constraints

Do not modify by default:

```text
- app/**
- database/migrations/**
- routes/**
- frontend source
- API Resource contracts
- PHP seeders
```

Allowed by default:

```text
- storage/app/blocks/**/*.json
- .agents/reports/*.md
```

## Stop conditions

Stop and report if:

```text
- expected source directories are missing
- JSON structure is ambiguous
- a task requires PHP code changes
- source data contradicts the seeding contract
```

## Final output

Create/update reports for each task and summarize:

```text
- files changed
- files skipped
- seeding commands to run
- endpoints to verify
- unresolved human decisions
```
