# RUN-BE-005 — Backend Contract Safety Net Workflow

## Purpose

This workflow explains how to run `TASK-BE-005` after the completed `TASK-BE-004` audit.

## Current phase

```text
Phase A — Stabilize before refactor
```

The purpose is not to fix the findings yet.
The purpose is to make the findings safe to fix later.

## Execution order

```text
1. Confirm Stage 1–9 materials are in repo.
2. Confirm AUDIT-BE-004 report exists.
3. Add TASK-BE-005 package.
4. Launch BE-005.
5. Review test files and BE-005 report.
6. Only then run BE-006/007/008 as separate tasks.
```

## Human launch command — PowerShell clipboard helper

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\tasks\LAUNCH-BE-005-backend-contract-safety-net.md -Raw | Set-Clipboard
```

Paste the clipboard content into Antigravity as the next task.

## Optional Codex CLI launch

```powershell
codex --cd C:\OSPanel\home\wsapi --sandbox workspace-write --ask-for-approval on-request "$(Get-Content C:\OSPanel\home\wsapi\.agents\tasks\LAUNCH-BE-005-backend-contract-safety-net.md -Raw)"
```

## Expected git status after run

Expected changes should mostly be:

```text
?? tests/Feature/BlockCategoryServicesContractTest.php
?? tests/Feature/BlockCategoryOffersContractTest.php
?? tests/Unit/EavContentResolverTest.php
?? tests/Unit/BlockAttachMapTest.php
?? .agents/reports/REPORT-BE-005-backend-contract-safety-net.md
```

Potentially modified:

```text
tests/Pest.php
tests/TestCase.php
phpunit.xml
```

Unexpected for this task:

```text
M app/Http/Controllers/**
M app/Http/Resources/**
M app/Repositories/**
M app/Support/**
M routes/api.php
M bootstrap/app.php
M app/Providers/AppServiceProvider.php
M database/migrations/**
M database/seeders/**
```

If those production files are changed, review carefully and usually reject unless the report provides a strong test-enablement reason.

## Review focus

Check:

```text
- Did the agent cover /en/blocks/categories/services?
- Did the agent cover /en/blocks/categories/offers/{slug}?
- Did it protect legacy keys: section, content, sections, subcategories, childs?
- Did it preserve the offers flat response contract?
- Did it run tests or honestly report inability?
- Did it avoid production refactor?
```

