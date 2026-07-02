# LAUNCH — TASK-BE-004 Complex Backend Architecture Audit

Run this from the backend repository root.

You are operating inside WS CODE backend repository.

## Mission

Execute:

```text
.agents/tasks/TASK-BE-004-complex-backend-architecture-audit.md
```

This is a **read-only architecture audit**.

Do not refactor code.
Do not modify application files.
Do not create TASK-BE-006+ files.
Do not install dependencies.
Do not run destructive database commands.

Allowed output file:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

## Required behavior

1. Read `AGENTS.md` and `.agents/agents.md` first.
2. Read `.agents/METHOD.md`, `.agents/RUNBOOK.md`, `.agents/REVIEW-CHECKLIST.md`.
3. Read the TASK-BE-004 file fully.
4. Inspect the current repository, especially backend source files, existing `.agents` materials, TASK-BE-002/TASK-BE-003, `CategoryPayloadAssembler`, `ImportHelper`, routes, resources, repositories, seeders, tests, Filament resources, and forms subsystem.
5. Use modern Laravel/community practice only as calibration, not as an excuse for broad generic advice.
6. Create the audit report at the required path.
7. In the final response, summarize only:
   - report file created
   - commands run
   - top 5 findings
   - recommended next task
   - whether TASK-BE-005 should be adjusted

## Strict constraints

Application code must remain unchanged.

Allowed changes:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

Forbidden changes:

```text
app/**
database/**
routes/**
config/**
composer.json
composer.lock
phpunit.xml
frontend files
```

Safe commands only:

```bash
php artisan route:list
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
composer validate
```

If a command fails because the environment is incomplete, record the failure honestly in the report.

## Start

Proceed with TASK-BE-004 now.
