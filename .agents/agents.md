# WebSolutions Agent Instructions

## Identity

You are working inside the WebSolutions project.

This is an existing production-oriented codebase with Vue frontend, Laravel backend, dynamic content structures, EAV-style data, and evolving agent-oriented architecture documentation.

Do not treat the project as greenfield.

---

## Primary Instruction Sources

Use this priority order:

```text
1. AGENTS.md
2. .agents/agents.md
3. Explicit user/task instruction
4. .agents/info/INDEX.md
5. .agents/info/MATERIALS-MAP.md
6. Relevant .agents/info/*.md architecture files
7. Relevant .agents/info/improvements/*.md AIP files
8. Relevant .agents/skills/*.md files
9. Source code
10. .gemini/ legacy context, only as secondary reference
```

`.gemini/` must not override `.agents/*`.

---

## Default Execution Protocol

For non-trivial work, follow:

```text
1. Read the task fully.
2. Identify whether the task is analysis-only or code-changing.
3. Read relevant architecture docs from `.agents/info`.
4. Read relevant AIP documents if the task touches planned improvements.
5. Inspect actual source files before editing.
6. Map the current flow.
7. Identify public contracts and compatibility risks.
8. Choose the smallest coherent improvement strategy.
9. Edit only the necessary files.
10. Validate or provide manual regression instructions.
11. Report facts, assumptions, risks, and next steps.
```

---

## No Hidden Assumptions

Do not assume that a field, method, relation, seeder, component, or legacy key is unused because it looks strange.

Examples of compatibility-sensitive names:

```text
childs
children
subcategories
acticle
items
locale
scope
section
content
sections
blocks
```

These may be ugly, but they may be public contract or legacy compatibility fields.

---

## Architecture Improvement Proposals

AIP files live in:

```text
.agents/info/improvements/
```

They describe proposed directions, tradeoffs, and staged plans.

AIP files are not direct execution tasks.

Do not implement an AIP unless a task explicitly asks you to.

When implementing a task related to an AIP area, use the AIP to understand:

```text
- known risks
- accepted constraints
- possible strategies
- open questions
- compatibility boundaries
```

---

## Backend Work Rules

### Current backend model

The backend includes a dynamic blocks/content system built around:

```text
blocks
blocks_categories
block_items
block_item_properties
block_item_property_values
```

The backend acts as a headless content/API layer for the Vue frontend.

### Read-side rule

```text
Repository / query layer prepares data.
Resource serializes prepared data.
EavContentResolver transforms EAV values.
BlockAttachMap applies current compatibility routing policy.
```

Resources should not perform hidden SQL queries.

If a Resource appears to compensate for incomplete model loading, analyze whether the preparation belongs in Repository, query layer, or a small assembler/read-model collaborator.

### Compatibility rule

For category endpoints, preserve public response shape unless the task explicitly approves a breaking change.

Especially preserve:

```text
data.content
data.sections
data.subcategories
data.blocks
data.children
subcategories[].id
subcategories[].slug
subcategories[].childs
subcategories[].title
subcategories[].descr
subcategories[].content
subcategories[].metadata
subcategories[].priority
```

---

## Frontend Work Rules

The frontend uses Vue 3, Vue Router, Pinia, Vite, Tailwind/Bootstrap transition, WS design system primitives, and GSAP animation orchestration.

Default component boundaries:

```text
View.vue = composition only
index.vue = orchestration/fetch/store layer
presentation components = props-only rendering
UI primitives = domain-agnostic layout/visual behavior
```

Do not introduce direct API/store dependencies into presentation components unless the relevant architecture document allows it.

---

## Refactoring Rules

Refactoring means improving structure while preserving behavior.

Allowed when justified:

```text
- move logic to the correct layer
- reduce hidden data lifting
- split oversized methods
- introduce small collaborators
- improve naming internally while keeping public aliases
- reduce duplication
- add compatibility wrappers
- add manual regression notes
```

Not allowed without explicit task permission:

```text
- changing database schema
- changing endpoint URLs
- renaming public JSON keys
- deleting compatibility fields
- rewriting unrelated modules
- touching frontend during backend-only work
- changing seed content while refactoring read-side API
- implementing AIP proposals directly without task approval
```

---

## Validation Expectations

Use available project validation commands.

If commands are unavailable or environment is incomplete, state that clearly and provide manual regression checks.

For backend API shape work, prefer comparing real endpoint output against known payload references when possible.

For frontend work, prefer at least build/test commands if available.

Do not claim runtime validation if only syntax checks were run.

---

## Reporting Requirements

Final report must be concrete.

Include:

```text
1. Context read
2. Files inspected
3. Files changed
4. What changed
5. Why it improves the system
6. What behavior/contract was preserved
7. Validation performed
8. Validation not performed and why
9. Remaining risks
10. Recommended next step
```

Avoid vague success claims.

If something is uncertain, say so.

---

## Core Rule

The project rule is:

```text
Improve without breaking.
```

Do not optimize for theoretical elegance over working compatibility.

Do not preserve bad structure forever when a task explicitly asks for architectural refactor.

Balance both:

```text
safe compatibility + real structural improvement
```
