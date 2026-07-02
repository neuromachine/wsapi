# AGENTS.md — WebSolutions Backend Agent Entry

## Purpose

This file is the root entry point for coding agents working in the WebSolutions repository.
It is intentionally short. Detailed context lives in `.agents/info/`; reusable operational guidance lives in `.agents/skills/`; concrete work requests live in `.agents/tasks/`.

## Context priority

Use this priority order when working in the repository:

1. Explicit user prompt for the current run.
2. The selected task file in `.agents/tasks/`.
3. This root `AGENTS.md`.
4. `.agents/agents.md`.
5. Relevant `.agents/info/*.md` documents.
6. Relevant `.agents/skills/*.md` documents.
7. Existing source code.

The `.gemini/` directory is secondary / legacy context for this project. Do not prefer `.gemini/` over `AGENTS.md` or `.agents/*` unless the user explicitly asks.

## Current backend focus

The current backend focus is a surgical refactor of the category endpoint chain:

```text
GET /en/blocks/categories/services
```

Target problem:

```text
app/Http/Resources/BlockCategoryResource.php currently performs SQL/model querying inside resolveSubitems().
```

Primary architectural rule:

```text
Repository prepares the model graph.
Resource serializes already prepared data.
EavContentResolver transforms already loaded EAV item collections.
BlockAttachMap remains a routing policy for block output.
```

## Hard constraints for TASK-BE-001

Do not change these unless a future task explicitly says so:

- Routes or endpoint names.
- Database schema or migrations.
- Public JSON response shape for `/en/blocks/categories/services`.
- Frontend files.
- `BlockAttachMap` policy.
- `EavContentResolver` behavior.
- Naming keys such as `subcategories`, `children`, or `childs`.

For the first backend pass, prefer a minimal diff that moves data preparation into the repository and removes SQL from the resource.

## Validation baseline

Use the saved `services.json` payload as the manual regression reference for the category endpoint response shape.

A successful run should preserve the public response contract and remove SQL/model access from `BlockCategoryResource`.
