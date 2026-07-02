# .agents — WebSolutions Agent Workspace

Human-readable guide for the agent context package.

## What this folder contains

```text
.agents/
  README.md                # human-readable orientation
  agents.md                # machine-oriented bootstrap manifest
  info/                    # architecture/context documents already placed by the project owner
  skills/                  # reusable backend/frontend agent instructions already placed by the project owner
  tasks/                   # concrete runnable task files
```

## Current active backend task

```text
.agents/tasks/TASK-BE-001-block-category-resource-boundary-refactor.md
```

This task starts the backend refactor track with the safest accepted path: **Variant A — minimal surgical boundary refactor**.

It focuses on:

```text
GET /en/blocks/categories/services
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
```

The goal is not to redesign the backend. The goal is to restore the intended layer boundary:

```text
Repository prepares.
Resource serializes.
```

## Important project note

The root `.gemini/` folder is secondary / legacy context. It may be useful for Antigravity, but it must not override:

```text
AGENTS.md
.agents/agents.md
.agents/info/*
.agents/skills/*
.agents/tasks/*
```

## Suggested launch prompt

Use the same prompt in Codex and Antigravity for comparison:

```text
Use project agent instructions and execute:
.agents/tasks/TASK-BE-001-block-category-resource-boundary-refactor.md

Treat .gemini/ as secondary legacy context.
Preserve the public JSON response shape for GET /en/blocks/categories/services.
```

## What to check after an agent run

Before accepting changes, review the diff and confirm:

```text
- BlockCategoryResource no longer imports or queries BlocksCategories.
- resolveSubitems() does not perform SQL/model querying.
- BlockCategoryRepository prepares enough child/subcategory data.
- response shape for /en/blocks/categories/services remains compatible with services.json.
- frontend files were not changed.
- routes and database schema were not changed.
- BlockAttachMap and EavContentResolver were not broadly refactored.
```

## Do not skip manual review

This first backend pass is a calibration run for coding agents. The diff quality matters as much as the result.
