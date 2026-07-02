# AIP-000 — Architecture Improvement Proposals Index

## Status

Draft.

## Purpose

This directory contains Architecture Improvement Proposals for the WebSolutions project.

AIP documents are not tasks, not mandatory implementation instructions, and not direct code-agent prompts. They are a controlled space for analyzing possible architecture improvements before turning them into `.agents/tasks/*`.

The purpose of this layer is to reduce premature task writing and avoid narrow patches based on incomplete assumptions.

---

## Why AIP Layer Exists

The project has already accumulated several architecture description layers:

```text
.agents/info/        — architecture and methodology context
.agents/skills/      — reusable code-agent guidance
.agents/tasks/       — executable task prompts
```

During the first backend refactoring cycle, a narrow task focused on removing SQL from `BlockCategoryResource::resolveSubitems()` revealed a methodological issue: the task was technically reasonable, but too confident about a local fix before the whole read-side data lifting system had been reconsidered.

The AIP layer is introduced to separate three activities:

```text
1. Describe current architecture.
2. Discuss possible architecture improvements.
3. Only then create code-agent tasks.
```

This makes future tasks less speculative and more aligned with real project constraints.

---

## What an AIP Is

An AIP is a structured architecture proposal.

It may describe:

```text
- a known architectural problem
- current behavior
- risks of the current behavior
- several possible improvement paths
- compatibility constraints
- decision criteria
- recommended next steps
```

An AIP can be used by a human architect/developer to decide what should become a concrete task later.

---

## What an AIP Is Not

An AIP is not:

```text
- a direct instruction to modify code
- a Codex/Antigravity task
- a final architectural decision by default
- a migration plan unless explicitly accepted
- a permission to break API contracts
- a replacement for `.agents/tasks/*`
```

If an AIP proposes a change, that change still needs an accepted task before implementation.

---

## AIP Statuses

Use these statuses when maintaining AIP files:

```text
Draft       — idea under discussion; not accepted for implementation.
Accepted    — direction accepted; may be converted into a task.
Postponed   — valid, but not relevant for current cycle.
Rejected    — considered and intentionally not used.
Superseded  — replaced by a newer AIP.
```

AIP files generated in this phase should usually start as `Draft`.

---

## Recommended AIP Structure

A typical AIP should contain:

```text
# AIP-XXX — Title

## Status
## Scope
## Problem
## Current Behavior
## Why This Matters
## Constraints
## Options
## Recommended Direction
## Risks
## What Not To Do Yet
## How This Could Become a Task
```

The structure may be adjusted when needed, but the document must clearly separate analysis from implementation.

---

## Current AIP List

### Backend

```text
AIP-BE-001-read-side-refactor.md
```

Focus: complex backend read-side data lifting, category endpoint response assembly, Repository/Resource/Resolver boundaries, and possible evolution toward assembler/read-model layers.

Planned later:

```text
AIP-BE-002-category-response-contract.md
AIP-BE-003-resource-to-assembler-boundary.md
AIP-BE-004-seeders-import-pipeline.md
AIP-BE-005-naming-and-compatibility-layer.md
```

### Frontend

Planned later:

```text
AIP-FE-001-server-driven-ui-path.md
```

---

## Relationship to Existing Backend Info Files

AIP documents depend on the backend info layer:

```text
BE-00-overview.md
BE-01-routing-and-scope.md
BE-02-blocks-read-side-flow.md
BE-03-eav-domain.md
BE-04-repository-layer.md
BE-05-resource-layer.md
BE-06-eav-content-resolver.md
BE-07-block-attach-map.md
BE-08-content-seed-pipeline.md
BE-CATEGORY-ENDPOINT-CONTRACT.md
BE-RESOURCE-BOUNDARY.md
```

The info files describe what exists.

The AIP files discuss what may be improved.

---

## Relationship to Skills

Skills should not contain large architecture debates.

Skills should encode accepted reusable guidance, for example:

```text
- preserve public API contracts
- inspect before editing
- do not make Resources perform SQL
- compare endpoint output before/after
```

AIP files may later influence skills, but they should not be copied into skills wholesale.

---

## Relationship to Tasks

Tasks should be created only after the relevant AIP direction is accepted.

Example flow:

```text
AIP-BE-001-read-side-refactor.md
  → discussion / correction / acceptance
    → TASK-BE-002-complex-read-side-refactor.md
      → code-agent execution
        → human review
```

This prevents code agents from executing speculative architectural ideas too early.

---

## Compatibility Principle

All AIP documents must preserve the core project rule:

```text
Improve without breaking.
```

In practice this means:

```text
- do not break frontend consumers accidentally
- do not rename public response keys without compatibility layer
- do not change DB schema unless explicitly justified and accepted
- do not treat ugly legacy names as unused
- do not assume a field is dead without checking consumers
```

---

## Current Priority

The immediate AIP priority is backend read-side architecture.

Reason:

```text
The current system already works, but the process of lifting category/block/EAV data into frontend-ready JSON has grown complicated and partly ambiguous.
```

The goal is not theoretical purity.

The goal is a safer architecture that can grow under real project constraints.

---

## How To Read This Directory

Recommended order:

```text
1. Read this index.
2. Read BE-02-blocks-read-side-flow.md.
3. Read BE-CATEGORY-ENDPOINT-CONTRACT.md.
4. Read AIP-BE-001-read-side-refactor.md.
5. Decide which direction should become a future task.
```

---

## Maintenance Rule

When a task is created from an AIP, update the relevant AIP with:

```text
- accepted direction
- related task file
- date or cycle marker if useful
- known limitations
```

Do not let AIP documents become stale wishlists disconnected from actual code work.
