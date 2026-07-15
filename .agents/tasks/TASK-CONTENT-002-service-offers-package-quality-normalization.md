# TASK-CONTENT-002 — Service Offers Package Quality Normalization

## Status

Practical content normalization task.

## Priority

Medium / after CONTENT-001.

## Goal

Normalize the quality and consistency of service offer packages after English coverage is completed.

This is not a translation task. It is a content quality pass.

## Scope

Target files:

```text
storage/app/blocks/items/*.json
```

## What to inspect

For each service offer file, review:

```text
- package count
- package naming consistency
- price/term formatting
- featured package logic
- feature duplication
- icon/index completeness
- package progression clarity
- description usefulness
```

## What to change

Allowed:

```text
- improve package names
- normalize prices/terms format
- remove duplicate features
- improve desc text
- ensure featured package is the main recommended package
- align EN package quality with RU structure
```

Forbidden:

```text
- changing PHP seeders
- changing endpoint shape
- deleting categories
- renaming category keys
- introducing calculator-specific fields unless requested
```

## Required report

Create:

```text
.agents/reports/REPORT-CONTENT-002-service-offers-package-quality-normalization.md
```

Report must include:

```text
- changed files
- package quality issues found
- normalization decisions
- categories needing human price review
```
