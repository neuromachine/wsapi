# Stage 13 — Seed Content Agent Package

This package switches the project from architecture refactoring into practical content seeding work.

It documents the current seeding/import system and adds two practical tasks:

```text
TASK-SEED-001 — Service Offers Locale Expansion
TASK-SEED-002 — Individual Offers Category and Package Data Normalization
```

Use this package inside the Laravel backend repository root.

## Intended focus

```text
Data/content work first.
No broad backend refactor.
No frontend work yet.
Do not turn this into another architecture audit.
```

## Execution order

```text
1. Read .agents/info/SEEDING-CONTEXT-MAP.md
2. Read .agents/contracts/CONTENT-FILE-FORMATS.md
3. Read .agents/contracts/OFFERS-SEEDING-CONTRACT.md
4. Run TASK-SEED-001
5. Run TASK-SEED-002
```

## Human launch

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\workflows\RUN-SEEDING-PRACTICE.md -Raw | Set-Clipboard
```

Paste into Antigravity.

