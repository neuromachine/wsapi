# TASK-BE-012 — Data Quality Audit

## Status

Audit/support task after BE-011, or independently runnable as read-only data audit.

## Type

Data quality audit / reporting. Mostly read-only.

## Priority

P2.

## Problem

The project contains EAV data, seeded JSON content, localized values, and historical content artifacts. Potential issues include:

```text
- encoding artifacts
- missing locales
- invalid JSON values
- duplicate keys
- inconsistent value_type vs actual value
- orphaned property values
- blocks/categories/items missing expected relations
```

These issues should be identified before deeper content-platform changes.

## Main Goal

Create a practical data quality report for the Blocks/EAV content system.

This task should not fix data by default.
It should report issues and propose follow-up data cleanup tasks.

## Primary Targets

Inspect through database or static available sources:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
storage/app/blocks/**
database/seeders/**
services.json if available
SQL dumps if available
```

## Audit Areas

### 1. Encoding artifacts

Search for suspicious characters in content values, for example:

```text
Ч
Т
�
mojibake-like replacements
```

Do not auto-fix.

### 2. Missing locales

For important items/properties, check presence of:

```text
ru
en
vi
```

Report gaps by item key and property key.

### 3. Invalid JSON

For values with:

```text
value_type = json
property.type = json
```

Check whether `value` is valid JSON.

### 4. Duplicate keys

Check duplicates or conflicts across:

```text
blocks.key
blocks_categories.key
block_items.key
block_item_properties key per block_id
```

### 5. EAV integrity

Check:

```text
property values without property
property values without item
items without block
items with missing category when expected
properties without block
```

### 6. Type consistency

Check whether `value_type` and `property.type` disagree in a meaningful way.

## Allowed Changes

Allowed:

```text
- create report files
- create small one-off diagnostic Artisan command only if useful and approved by task scope
- create SQL snippets or PHP snippets under .agents/reports or .agents/tools if non-invasive
```

## Forbidden Changes

Do not:

```text
- change production data
- change seed content
- change database schema
- change API resources
- change frontend
- auto-correct encoding
- delete duplicate records
```

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-012-data-quality-audit.md
```

Optionally create machine-readable report:

```text
.agents/reports/REPORT-BE-012-data-quality-findings.json
```

Report structure:

```text
1. Scope inspected.
2. Data source used: DB / SQL dump / JSON files / static only.
3. Encoding findings.
4. Missing locale findings.
5. Invalid JSON findings.
6. Duplicate key findings.
7. EAV integrity findings.
8. Type consistency findings.
9. Recommended cleanup tasks.
```

## Success Criteria

```text
- data risks are visible and grouped
- no production data is changed
- findings are actionable
- future cleanup can be split into small tasks
```

