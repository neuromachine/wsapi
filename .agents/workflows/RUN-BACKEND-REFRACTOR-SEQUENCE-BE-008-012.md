# RUN — Backend Refactor Sequence BE-008 → BE-012

## Current Known Status

```text
BE-006 — completed: route/bootstrap cleanup
BE-007 — completed: offers endpoint boundary refactor
```

Do not evaluate those runs in this workflow. Their quality review can be done separately.

## Next Execution Order

```text
1. BE-008 — explicit Resource serialization
2. BE-009 — EAV model relation cleanup
3. BE-010 — EAV resolver / BlockItemResource consistency
4. BE-011 — Filament EAV guardrails
5. BE-012 — data quality audit
```

## Operator Mode

For each task:

```text
1. Copy LAUNCH-BE-00X...md to Antigravity.
2. Let the agent make code changes.
3. Review git diff.
4. Run the simplest manual endpoint check you can.
5. Keep the report.
6. Continue to the next task if the diff is coherent.
```

Tests are useful but not the main blocker in this sequence.

## PowerShell Clipboard Commands

### BE-008

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\tasks\LAUNCH-BE-008-explicit-resource-serialization-hardening.md -Raw | Set-Clipboard
```

### BE-009

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\tasks\LAUNCH-BE-009-eav-model-relation-cleanup.md -Raw | Set-Clipboard
```

### BE-010

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\tasks\LAUNCH-BE-010-eav-resolver-block-item-resource-consistency.md -Raw | Set-Clipboard
```

### BE-011

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\tasks\LAUNCH-BE-011-filament-eav-guardrails.md -Raw | Set-Clipboard
```

### BE-012

```powershell
cd C:\OSPanel\home\wsapi
Get-Content .agents\tasks\LAUNCH-BE-012-data-quality-audit.md -Raw | Set-Clipboard
```

## Minimal Manual Checks

After BE-008/009/010:

```text
/api/en/blocks/categories/services
/api/en/blocks/categories/offers/{slug}
```

After BE-011:

```text
Open Filament resource pages and verify forms still load.
```

After BE-012:

```text
Read the report and split cleanup into future tasks.
```

