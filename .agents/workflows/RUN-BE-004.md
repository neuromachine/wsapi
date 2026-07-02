# RUN-BE-004 — How to Launch the Backend Architecture Audit

## Goal

Run TASK-BE-004 in the backend repository after Stage 1–9 materials have been copied into the repo.

The agent should create only one report:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

## Recommended Antigravity / agent UI flow

1. Open the backend repository as the active project/workspace.
2. Ensure Stage 1–9 materials and this package are present.
3. Open or paste:

```text
.agents/tasks/LAUNCH-BE-004-complex-backend-architecture-audit.md
```

4. Start the agent run.
5. Reject the run if it tries to modify application code.
6. Accept only the report file under `.agents/reports/`.

## PowerShell helper: copy launch prompt

From backend repo root:

```powershell
Get-Content .agents\tasks\LAUNCH-BE-004-complex-backend-architecture-audit.md -Raw | Set-Clipboard
```

Then paste the prompt into Antigravity/Codex UI.

## Bash helper: print launch prompt

From backend repo root:

```bash
cat .agents/tasks/LAUNCH-BE-004-complex-backend-architecture-audit.md
```

## Codex CLI example

From any directory, replace the path with your backend repo path:

```bash
codex --cd /path/to/wsapi --sandbox workspace-write --ask-for-approval on-request "$(cat /path/to/wsapi/.agents/tasks/LAUNCH-BE-004-complex-backend-architecture-audit.md)"
```

On Windows PowerShell:

```powershell
codex --cd C:\OSPanel\home\wsapi --sandbox workspace-write --ask-for-approval on-request "$(Get-Content C:\OSPanel\home\wsapi\.agents\tasks\LAUNCH-BE-004-complex-backend-architecture-audit.md -Raw)"
```

## Review after run

Check:

```text
- app/** unchanged
- database/** unchanged
- routes/** unchanged
- config/** unchanged
- only .agents/reports/AUDIT-BE-004-backend-architecture-audit.md added
```

Suggested git check:

```bash
git status --short
```

Expected shape:

```text
?? .agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

If other files changed, review carefully and reject unless you explicitly approved those edits.
