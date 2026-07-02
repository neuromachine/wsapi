# WebSolutions Architecture Materials — Index

## Purpose

This directory contains architecture and context-engineering materials for the WebSolutions project.

The materials are intended for two audiences:

```text
1. Human maintainer / architect
   Uses these files to understand the system, plan refactoring, and review agent output.

2. Code agent / ML system
   Uses these files as structured project context before analyzing or modifying code.
```

The goal is not to create decorative documentation. The goal is to preserve architectural context, reduce repeated explanation, and make future Codex / Antigravity / similar agent runs safer.

---

## Current Project Focus

The project is a modular web platform:

```text
Frontend:
  Vue 3 + Vue Router + Pinia + Vite

Backend:
  Laravel API with block/category/content EAV-style data model

Agent layer:
  .agents-based context engineering package for code-agent refactoring
```

The current practical focus is backend and frontend refactoring without breaking existing behavior.

Especially important current backend theme:

```text
Complex and increasingly fragile read-side data lifting for block/category endpoints.
```

Reference area:

```text
GET /en/blocks/categories/services
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
```

---

## Directory Roles

Recommended structure:

```text
project-root/
  AGENTS.md
  .agents/
    README.md
    agents.md
    info/
      INDEX.md
      MATERIALS-MAP.md
      GLOSSARY.md
      ...architecture docs...
    skills/
      ...agent skills...
    tasks/
      ...execution tasks...
```

### `AGENTS.md`

Root-level instruction entry point for code agents.

Use for durable, short, high-priority guidance.

Should not contain the full architecture.

### `.agents/README.md`

Human-facing guide explaining how to use the `.agents` package.

### `.agents/agents.md`

Machine-facing runtime protocol for agents.

### `.agents/info/`

Architecture and project context.

This is the main documentation layer.

### `.agents/skills/`

Reusable focused instructions for agents.

Skills should be short, operational, and task-relevant.

### `.agents/tasks/`

Concrete execution tasks.

Tasks should reference `info` and `skills`, but should not duplicate them fully.

---

## How to Read These Materials

### Human reading order

Recommended order for a human architect/developer:

```text
1. INDEX.md
2. MATERIALS-MAP.md
3. GLOSSARY.md
4. SYSTEM.md
5. Frontend architecture docs
6. Backend architecture docs
7. Improvement proposals
8. Skills
9. Tasks
```

### Agent reading order

Recommended order for a code agent:

```text
1. AGENTS.md
2. .agents/agents.md
3. Relevant task file
4. Relevant info docs named by the task
5. Relevant skills named by the task
6. Source files
```

Agents should not read everything by default if the task is narrow. They should read progressively.

---

## Material Groups

### Core Context

Core project identity and architectural baseline.

Expected files:

```text
SYSTEM.md
INDEX.md
MATERIALS-MAP.md
GLOSSARY.md
```

### Frontend Architecture

Describes Vue frontend architecture and UI layers.

Expected files:

```text
DS.md
TL.md
AL.md
CA.md
DM.md
FE-ARCHITECTURE-SUMMARY.md
FE-BACKEND-CONTRACT-BRIDGE.md
```

Current known frontend topics:

```text
- Vue Foundation / Component Architecture
- View.vue → index.vue → presentation → item
- Pinia blockStore factory
- usePageOrchestrator
- Data Mapping
- WS Design System
- Tailwind Theme Layer
- Animation Layer
```

### Backend Architecture

Describes Laravel API, block content model, EAV model, repositories, resources, and read-side flow.

Expected files:

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

Current known backend topics:

```text
- Laravel API backend
- block/category EAV-style data model
- Repository prepares data
- Resource serializes data
- EavContentResolver transforms EAV into frontend-ready objects
- BlockAttachMap routes block output into content / sections / blocks
- category endpoint response contract must be preserved
```

### Improvement Proposals

Architecture Improvement Proposals are not tasks.

They document possible directions and decision criteria before code-agent execution.

Expected folder:

```text
.agents/info/improvements/
```

Expected files:

```text
AIP-000-index.md
AIP-BE-001-read-side-refactor.md
AIP-BE-002-category-response-contract.md
AIP-BE-003-resource-to-assembler-boundary.md
AIP-BE-004-seeders-import-pipeline.md
AIP-BE-005-naming-and-compatibility-layer.md
AIP-FE-001-server-driven-ui-path.md
```

### Skills

Reusable instruction modules for code agents.

Expected folder:

```text
.agents/skills/
```

Skills should focus on behavior and constraints, not on one-off code edits.

### Tasks

Concrete execution prompts.

Expected folder:

```text
.agents/tasks/
```

Tasks should be explicit about:

```text
- goal
- target files
- required context
- allowed changes
- forbidden changes
- compatibility constraints
- validation
- report format
```

---

## Current Methodological Direction

The current methodology is shifting from narrow patch prompts toward architecture-safe refactoring.

Preferred agent workflow:

```text
inspect
  → map current behavior
    → identify contracts
      → propose refactor strategy
        → implement minimal coherent changes
          → validate compatibility
            → report risks and next steps
```

Avoid:

```text
- overfitting to a presumed solution
- broad rewrite without contract analysis
- changing public API shape accidentally
- treating ugly legacy names as safe to rename
- mixing read-side refactor with seed/import refactor
```

---

## Compatibility First Rule

The project contains working but evolving architecture.

Some names and shapes are not ideal but are currently public or semi-public contracts.

Examples:

```text
subcategories
children
childs
acticle
items.items
locale / scope / section
```

Do not rename or remove these casually.

Use compatibility layers, aliases, and documented migrations when needed.

---

## Current Backend Refactoring Position

The previous narrow backend attempt showed an important lesson:

```text
A task that is too narrow can push an agent to satisfy a local instruction while missing the larger system design problem.
```

Current backend direction:

```text
Do not merely move one query.
Understand and improve the read-side data lifting system.
Preserve response contracts.
Refactor in stages.
```

This affects future materials:

```text
- AIP files must be created before large tasks.
- Skills must encourage analysis-before-editing.
- Tasks must not prescribe speculative implementation details too early.
```

---

## What This Index Is Not

This file is not:

```text
- a task prompt
- a coding instruction by itself
- a full architecture document
- a replacement for source code inspection
- a final decision log
```

It is the entry point and navigation document for the documentation set.

