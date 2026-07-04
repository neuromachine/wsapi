# TASK-CONTENT-003 — EN Category Descriptions for Service Catalog

## Status

Practical content completion task.

## Priority

Medium.

## Goal

Complete or improve English descriptions for service catalog categories so English frontend/service pages are not empty, Russian-only, or weakly localized.

## Scope

Target paths:

```text
storage/app/blocks/cat/*.json
storage/app/blocks/cat/{group}/*.json
```

## What to do

1. Scan service category JSON sources.
2. Identify category files with missing, empty, or Russian-only English content.
3. Add concise English content according to existing file structure.
4. Preserve category keys and tree relationships.
5. Do not rewrite the tree itself.

## Contract

Follow:

```text
.agents/contracts/SERVICE-CATEGORY-DESCRIPTION-CONTRACT.md
```

## Required report

Create:

```text
.agents/reports/REPORT-CONTENT-003-en-service-category-descriptions.md
```

Report must include:

```text
- files scanned
- files changed
- files skipped and why
- categories still requiring content strategy
```
