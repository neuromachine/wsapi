# LAUNCH — TASK-BE-009 EAV Model Relation Cleanup

Execute:

```text
.agents/tasks/TASK-BE-009-eav-model-relation-cleanup.md
```

## Mode

Practical code refactor.

## Goal

Clean up duplicate/ambiguous EAV relation names on `BlockItem`.

Prefer `propertyValues()` as canonical.
Keep `properties()` as deprecated alias if needed for compatibility.

Do not change public API response keys.
Do not change database schema.
Do not change frontend.

Expected report:

```text
.agents/reports/REPORT-BE-009-eav-model-relation-cleanup.md
```

