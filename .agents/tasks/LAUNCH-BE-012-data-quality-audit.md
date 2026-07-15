# LAUNCH — TASK-BE-012 Data Quality Audit

Execute:

```text
.agents/tasks/TASK-BE-012-data-quality-audit.md
```

## Mode

Read-only audit/reporting task.

## Goal

Inspect Blocks/EAV/content data for encoding artifacts, missing locales, invalid JSON, duplicate keys, and EAV integrity issues.

Do not modify production data.
Do not change code unless creating a non-invasive diagnostic helper is clearly useful.

Expected report:

```text
.agents/reports/REPORT-BE-012-data-quality-audit.md
```

