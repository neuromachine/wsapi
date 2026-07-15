# TEMPLATE — Analysis-First Refactor

## Status

Task template.

Use this template when the expected change may affect architecture, data flow, public contracts, or multiple files.

This template is intentionally not a concrete task. Fill the placeholders before execution.

---

## Core Principle

Analyze before editing.

Do not begin with a patch. First understand the current flow, the real contract, the consumers, and the risk boundary.

---

## Task Name

`<TASK-ID> — <Short task title>`

Example:

```text
TASK-BE-002 — Complex Read-Side Refactor
```

---

## Context

Describe the project area and why this refactor exists.

```text
<Write the domain context here>
```

Include:

```text
- current module/system
- known pain points
- current working behavior
- why the code should be improved
- what must not break
```

---

## Primary Goal

```text
<Write the main outcome expected from the refactor>
```

The goal should describe improvement, not an assumed implementation.

Prefer:

```text
Improve the read-side data loading flow while preserving API compatibility.
```

Avoid:

```text
Move this exact method into this exact class.
```

Unless the implementation is already accepted.

---

## Scope

### Primary Files / Areas

```text
<file-or-folder-1>
<file-or-folder-2>
<file-or-folder-3>
```

### Secondary Files / Areas

```text
<optional file/folder list>
```

Secondary files may be changed only if justified.

---

## Required Context Intake

Before editing, inspect:

```text
.agents/README.md
.agents/agents.md
.agents/info/INDEX.md
.agents/info/MATERIALS-MAP.md
.agents/info/GLOSSARY.md
<task-specific info files>
<task-specific AIP files>
<task-specific skills>
```

If `.gemini/` exists, treat it as secondary legacy context. It must not override `.agents/*`.

---

## Required Analysis Before Editing

The agent must identify:

```text
1. Current execution/data flow.
2. Public or implicit contracts.
3. Known consumers.
4. Existing architectural boundaries.
5. Places where current code violates those boundaries.
6. Safe refactor opportunities.
7. Areas that should remain unchanged.
8. Regression checks required after changes.
```

The agent should not assume that a field, method, or file is unused without checking.

---

## Allowed Changes

The agent may:

```text
- refactor code to clarify responsibilities
- move logic to a better existing layer
- introduce a small helper/collaborator if justified
- reduce duplication
- make implicit compatibility behavior explicit
- add focused comments for transitional compatibility
- add or update lightweight tests if already consistent with the project
- provide manual regression notes
```

---

## Forbidden Changes

Do not:

```text
- change public response shape
- rename public keys without compatibility layer
- change routes or endpoint URLs
- change database schema
- modify unrelated frontend/backend layers
- rewrite the module from scratch
- introduce external dependencies
- remove legacy behavior without proving it is unused
- hide uncertainty in the final report
```

---

## Refactoring Rules

Use this sequence:

```text
inspect → map → propose → refactor → verify → report
```

Implementation must be smaller than the system it improves.

Prefer incremental improvement over theoretical perfection.

---

## Validation / Regression

Run available checks when possible:

```text
<project-specific command 1>
<project-specific command 2>
```

If runtime validation cannot be executed, state exactly why and provide a static/manual regression report.

Do not claim runtime verification if only syntax/static checks were run.

---

## Expected Final Report

The final response must include:

```text
1. Files inspected.
2. Files changed.
3. Current flow understood.
4. Refactor performed.
5. Public contract preserved.
6. Validation run.
7. What was intentionally not changed.
8. Remaining risks / recommended next step.
```

---

## Success Criteria

The task succeeds if:

```text
- current behavior remains compatible
- code responsibility boundaries are clearer
- hidden assumptions are reduced
- no broad unrelated rewrite occurred
- regression evidence is provided
```

---

## Failure Criteria

The task fails if:

```text
- compatibility is broken
- public keys are renamed without migration
- the agent edits before understanding
- the final report overclaims verification
- the result is more complex than the original without clear benefit
```
