# TASK-BE-011 — Filament EAV Guardrails

## Status

Follow-up practical backend/admin refactor after BE-010.

## Type

Admin safety / EAV data integrity.

## Priority

P2.

## Problem

Filament admin resources may allow manual creation or editing of EAV values in ways that can desynchronize:

```text
BlockItemProperty.type
BlockItemProperty.is_collection
BlockItemPropertyValue.value_type
BlockItemPropertyValue.value
```

This can cause API transformation inconsistencies later.

## Main Goal

Add practical guardrails to Filament EAV editing so admin-side changes are less likely to corrupt API-facing EAV data.

## Primary Targets

Inspect:

```text
app/Filament/Resources/BlockItemPropertyValueResource.php
app/Filament/Resources/BlockItemPropertyResource.php
app/Filament/Resources/BlockItemResource.php
app/Models/BlockItemProperty.php
app/Models/BlockItemPropertyValue.php
```

## Desired Direction

Prefer small practical protections:

```text
- make value_type selection derive from selected property where possible
- show related property type/is_collection clearly
- prevent impossible value_type choices when property is known
- add form helper text/warnings where full automation is unsafe
- avoid breaking existing admin workflows
```

If automatic value_type synchronization is too risky, implement read-only hints and leave the full sync as a later task.

## Allowed Changes

Allowed:

```text
- modify Filament form schema for BlockItemPropertyValueResource
- add helper text / disabled fields / reactive value_type behavior if safe
- add validation rules at Resource level if low-risk
- add model casts/fillable improvements only if directly related and safe
- update report
```

## Forbidden Changes

Do not:

```text
- change database schema
- change public API response shape
- change frontend
- rewrite Filament resources broadly
- remove admin fields without replacement
- force a data migration
- change EavContentResolver behavior
```

## Manual Verification

In Filament/admin UI, inspect:

```text
- creating/editing BlockItemPropertyValue
- selecting property
- value_type behavior
- value field behavior
- existing records still editable
```

API endpoints should remain unchanged:

```text
GET /api/en/blocks/categories/services
GET /api/en/blocks/categories/offers/{slug}
```

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-011-filament-eav-guardrails.md
```

Include:

```text
1. Files inspected.
2. Files changed.
3. Guardrails added.
4. What was intentionally not automated.
5. API compatibility statement.
6. Manual admin verification notes.
7. Recommended next task.
```

## Success Criteria

```text
- Admin-side EAV editing becomes safer.
- Existing records and workflows are not broken.
- API output remains unchanged.
- Changes are focused on Filament/admin layer.
```

