# LAUNCH — TASK-BE-005 Backend Contract Safety Net

You are working inside the WS CODE backend repository.

Execute:

```text
.agents/tasks/TASK-BE-005-backend-contract-safety-net.md
```

## Critical mode

This is a safety-net task, not a refactor task.

Do not refactor production architecture.
Do not change controllers/resources/repositories/support classes unless a tiny test-enablement change is strictly necessary and explicitly justified.

Your primary goal is to create backend contract tests for:

```text
GET /en/blocks/categories/services
GET /en/blocks/categories/offers/{slug}
```

You must also create the final report:

```text
.agents/reports/REPORT-BE-005-backend-contract-safety-net.md
```

## Read first

Read these files before acting:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
.agents/METHOD.md
.agents/RUNBOOK.md
.agents/REVIEW-CHECKLIST.md
.agents/tasks/TASK-BE-005-backend-contract-safety-net.md
```

Then inspect relevant backend files and tests.

## Output expectations

Create/modify only test-related files and the BE-005 report unless strictly necessary.

At the end, report:

```text
- files changed
- tests created
- test data strategy
- commands run
- pass/fail results
- whether BE-006/007/008 are safe to proceed
```

Do not claim success for commands that could not run.

