# AGENTS.md — WebSolutions Agent Runtime Entry

## Purpose

This file is the root instruction entry for coding agents working inside the WebSolutions project.

The project is a modular web platform with:

- Vue 3 / Vite frontend
- Laravel API backend
- dynamic block/content architecture
- EAV-style backend data model
- evolving `.agents` methodology for safe architecture-aware refactoring

Agents must treat this repository as an existing working system, not as a greenfield project.

---

## Context Priority

Read context in this order:

```text
1. AGENTS.md
2. .agents/agents.md
3. .agents/info/INDEX.md
4. .agents/info/MATERIALS-MAP.md
5. task-specific files in .agents/tasks/ if explicitly requested
6. relevant .agents/info/*.md architecture documents
7. relevant .agents/info/improvements/*.md AIP documents
8. relevant .agents/skills/*.md files
9. source code
10. .gemini/ legacy context only when explicitly useful
```

`.gemini/` is secondary legacy context. It must not override `.agents/*`.

If instructions conflict, follow the newest and most specific `.agents` instruction.

---

## Operating Mode

Default mode for this project:

```text
inspect → map → propose → refactor → verify → report
```

Do not jump directly into editing unless the task explicitly asks for a narrow mechanical change.

For architectural or refactoring tasks, first understand:

- current data flow
- public contracts
- frontend dependencies
- backend layer boundaries
- legacy compatibility fields
- known AIP proposals
- scope of allowed changes

---

## Compatibility First

The project contains active frontend/backend contracts.

Do not casually change:

```text
- API endpoint URLs
- Laravel Resource envelope shape
- public JSON keys
- category response structure
- frontend route expectations
- locale/scope behavior
- database schema
- seed content keys
```

A refactor is successful only if the system becomes clearer without breaking existing behavior.

---

## Backend Core Rule

For backend read-side work:

```text
Repository / query layer prepares data.
Resource serializes prepared data.
Resolver transforms EAV values.
Compatibility policy is explicit.
```

Resources should not perform hidden SQL queries or compensate for incomplete model loading.

---

## Frontend Core Rule

For frontend work:

```text
View composes.
Index/orchestrator fetches.
Presentation components receive props.
UI primitives stay domain-agnostic.
```

Do not import Pinia stores or API logic directly into presentation components unless the architecture document explicitly allows it.

---

## AIP Documents

Files under:

```text
.agents/info/improvements/
```

are Architecture Improvement Proposals.

They are not direct coding tasks.

Use them to understand possible future directions, risks, and accepted constraints. Do not implement an AIP unless a task explicitly instructs you to do so.

---

## Editing Rules

Before editing:

```text
1. Read the task.
2. Read relevant `.agents/info` files.
3. Read relevant AIP documents if the task touches improvement areas.
4. Inspect actual source code.
5. Identify public contracts that must be preserved.
6. State or internally commit to the smallest safe strategy.
```

During editing:

```text
- prefer coherent refactoring over cosmetic churn
- keep diffs reviewable
- avoid broad unrelated rewrites
- avoid new abstractions unless they reduce real complexity
- preserve compatibility keys even when names are imperfect
```

After editing:

```text
- run available validation commands
- if runtime validation is unavailable, provide manual regression notes
- report exactly what changed and what was intentionally left unchanged
```

---

## Reporting Format

Final reports should include:

```text
1. Context used
2. Files inspected
3. Files changed
4. What changed and why
5. Compatibility / contract preservation
6. Validation or manual regression
7. Remaining risks
8. Recommended next step
```

---

## Do Not Overfit

Do not assume the user wants a narrow patch just because one file looks problematic.

If the task is broad, analyze the system broadly before changing code.

If the task is narrow, do not expand it into an architectural rewrite.

The central project rule is:

```text
Improve without breaking.
```
