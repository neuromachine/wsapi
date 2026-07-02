# WebSolutions Architecture Materials — Map

## Purpose

This document maps the architecture, context-engineering, skills, task, and future improvement materials used in the WebSolutions project.

It should help both humans and agents understand:

```text
- which files exist
- what each file is responsible for
- which files are current source of truth
- which files are legacy or transitional
- which files should be read before a given type of work
```

This map is not a task. It does not instruct an agent to change code.

---

## High-Level Material Groups

```text
Core Context
Frontend Architecture
Backend Architecture
Endpoint / Contract Docs
Architecture Improvement Proposals
Agent Runtime Files
Agent Skills
Agent Tasks
Review / Runbook Materials
Legacy / Secondary Context
```

---

# 1. Core Context

## `SYSTEM.md`

Role:

```text
System-level WebSolutions Architecture Context.
```

Audience:

```text
Human + code agent.
```

Expected content:

```text
- project identity
- high-level stack
- architecture principles
- frontend/backend relationship
- code-agent working assumptions
```

Status:

```text
Core source of truth.
```

Use when:

```text
- starting a new agent session
- reviewing broad project context
- onboarding a new documentation pass
```

---

## `INDEX.md`

Role:

```text
Entry point for .agents/info materials.
```

Audience:

```text
Human primarily; agents may read for orientation.
```

Expected content:

```text
- how materials are grouped
- recommended reading order
- distinction between info / skills / tasks / AIP
```

Status:

```text
Navigation document.
```

---

## `MATERIALS-MAP.md`

Role:

```text
Inventory and dependency map of the documentation set.
```

Audience:

```text
Human + code agent.
```

Expected content:

```text
- list of architecture docs
- list of agent files
- planned improvement docs
- relationship between materials
```

Status:

```text
Navigation + governance document.
```

---

## `GLOSSARY.md`

Role:

```text
Shared vocabulary and term stabilization.
```

Audience:

```text
Human + code agent.
```

Expected content:

```text
- project terms
- current names
- target names where known
- compatibility notes
```

Status:

```text
Term source of truth.
```

Use when:

```text
- a task involves naming
- backend/frontend terms are ambiguous
- agent may confuse locale/scope/section or children/subcategories/childs
```

---

# 2. Frontend Architecture Materials

## `DS.md`

Role:

```text
WS Design System Layer.
```

Scope:

```text
- UI primitives
- component-driven design system
- Card / SectionHeader / slots
- composition over configuration
- boundary between feature components and reusable UI
```

Use when:

```text
- refactoring Vue presentation components
- working with UI primitives
- deciding whether logic belongs in Card or feature component
```

Agent warning:

```text
Do not turn UI primitives into backend-aware or business-specific components.
```

---

## `TL.md`

Role:

```text
Tailwind Theme Layer.
```

Scope:

```text
- Tailwind v4 theme system
- token / semantic / usage layers
- Bootstrap coexistence
- forbidden raw hex duplication
```

Use when:

```text
- changing classes
- creating new UI primitives
- migrating from legacy CSS/Bootstrap to Tailwind
```

Agent warning:

```text
Prefer semantic theme usage. Do not scatter raw design values in templates.
```

---

## `AL.md`

Role:

```text
Animation Layer.
```

Scope:

```text
- GSAP orchestration
- animationStore
- useGsapOrchestrator
- PAGE_ENTER / local / scroll streams
- no direct GSAP calls in component lifecycle
```

Use when:

```text
- adding animations
- refactoring animated components
- synchronizing dynamic content with ScrollTrigger
```

Agent warning:

```text
Animation logic must stay decoupled from data loading and UI structure.
```

---

## `CA.md`

Role:

```text
Vue Foundation / Component Architecture.
```

Scope:

```text
- View.vue → index.vue → presentation → item
- Pinia blockStore factory
- usePageOrchestrator
- page ownership
- route-driven data loading
```

Use when:

```text
- modifying Vue page/block lifecycle
- moving data loading logic
- deciding whether a component may import stores
```

Agent warning:

```text
Presentation components should receive props, not import blockStore directly.
```

---

## `DM.md`

Role:

```text
Data-driven / API Contract / Frontend Data Mapping Layer.
```

Scope:

```text
- response.data.data
- properties root
- section nodes
- compred / visarun_system payload shape
- rich HTML boundaries
- packages/items aliases
```

Use when:

```text
- mapping API payloads to Vue components
- preserving frontend data contracts
- working on compred-style pages
```

Agent warning:

```text
Do not make frontend components know backend EAV internals.
```

---

## Planned `FE-ARCHITECTURE-SUMMARY.md`

Role:

```text
Short consolidated frontend architecture summary.
```

Status:

```text
Planned.
```

Purpose:

```text
Give agents a fast frontend overview without forcing them to read every frontend layer document.
```

---

## Planned `FE-BACKEND-CONTRACT-BRIDGE.md`

Role:

```text
Bridge between backend API responses and frontend consumers.
```

Status:

```text
Planned.
```

Purpose:

```text
Clarify which backend response shapes are consumed by frontend pages/components.
```

---

# 3. Backend Architecture Materials

## `BE-00-overview.md`

Role:

```text
Backend architecture overview.
```

Scope:

```text
- Laravel API backend
- block/category module
- EAV read-side model
- repository/resource split
- current refactoring focus
```

Use when:

```text
- starting backend work
- understanding global backend boundaries
```

---

## `BE-03-eav-domain.md`

Role:

```text
EAV domain model description.
```

Scope:

```text
- Block
- BlockItem
- BlockItemProperty
- BlockItemPropertyValue
- BlocksCategories
- locale/version/value_type/is_collection
```

Use when:

```text
- modifying EAV-related logic
- debugging flattened payloads
- reasoning about dynamic content structures
```

---

## `BE-04-repository-layer.md`

Role:

```text
Repository layer documentation.
```

Scope:

```text
- query construction
- eager loading
- locale filtering
- prepared model graph
- current BlockCategoryRepository responsibilities
```

Use when:

```text
- changing backend data loading
- deciding whether logic belongs in Repository
- refactoring read-side flows
```

---

## `BE-05-resource-layer.md`

Role:

```text
Resource layer documentation.
```

Scope:

```text
- JSON serialization
- Resource boundaries
- current resource debt
- BlockCategoryResource risk area
```

Use when:

```text
- modifying API Resources
- removing SQL from Resources
- preserving public response shape
```

---

## `BE-CATEGORY-ENDPOINT-CONTRACT.md`

Role:

```text
Contract document for category endpoint response shape.
```

Scope:

```text
GET /{locale}/blocks/categories/{slug}
GET /en/blocks/categories/services
```

Preserves:

```text
data.content
data.sections
data.subcategories
data.blocks
data.children
```

Use when:

```text
- refactoring backend category endpoint
- validating services page compatibility
- writing or reviewing backend tasks
```

---

## `BE-RESOURCE-BOUNDARY.md`

Role:

```text
Boundary contract between Repository and Resource.
```

Scope:

```text
Repository prepares.
Resource serializes.
Resolver transforms.
```

Use when:

```text
- reviewing Resource refactors
- detecting SQL inside Resources
- designing read-side improvements
```

---

## Planned `BE-01-routing-and-scope.md`

Role:

```text
Routing, locale, scope, and section context.
```

Status:

```text
Planned.
```

---

## Planned `BE-02-blocks-read-side-flow.md`

Role:

```text
Central backend read-side flow document.
```

Status:

```text
Planned.
```

This will become one of the most important backend documents.

---

## Planned `BE-06-eav-content-resolver.md`

Role:

```text
Focused documentation for EavContentResolver.
```

Status:

```text
Planned.
```

---

## Planned `BE-07-block-attach-map.md`

Role:

```text
Focused documentation for BlockAttachMap.
```

Status:

```text
Planned.
```

---

## Planned `BE-08-content-seed-pipeline.md`

Role:

```text
Seeder and content import pipeline documentation.
```

Status:

```text
Planned.
```

---

# 4. Architecture Improvement Proposals

## Folder

```text
.agents/info/improvements/
```

## Role

Architecture Improvement Proposals describe possible future changes, decision criteria, risks, and migration strategies.

They are not tasks.

They should be reviewed and agreed before becoming code-agent tasks.

---

## Planned AIP files

```text
AIP-000-index.md
AIP-BE-001-read-side-refactor.md
AIP-BE-002-category-response-contract.md
AIP-BE-003-resource-to-assembler-boundary.md
AIP-BE-004-seeders-import-pipeline.md
AIP-BE-005-naming-and-compatibility-layer.md
AIP-FE-001-server-driven-ui-path.md
```

## Why AIP exists

The previous practical cycle showed that jumping directly from architecture docs to a narrow task can overfit agent behavior.

AIP files should answer first:

```text
- what are we trying to improve?
- what are the possible strategies?
- what is safe to change?
- what is contract-bound?
- what should remain postponed?
```

---

# 5. Agent Runtime Files

## `AGENTS.md`

Role:

```text
Root instruction file for code agents.
```

Status:

```text
Exists / should be periodically regenerated after architecture docs stabilize.
```

Principles:

```text
- short
- durable
- points to .agents/info and .agents/agents.md
- avoids duplicating full architecture
```

---

## `.agents/README.md`

Role:

```text
Human-readable guide to the .agents package.
```

Status:

```text
Exists / will be regenerated in Agent Runtime pass.
```

---

## `.agents/agents.md`

Role:

```text
Machine-readable agent execution protocol.
```

Status:

```text
Exists / will be regenerated in Agent Runtime pass.
```

Should emphasize:

```text
inspect first
preserve contracts
avoid hidden assumptions
report uncertainty
no broad rewrite unless task says so
```

---

# 6. Agent Skills

## Current role

Skills are reusable task-relevant instruction modules.

Current backend skills were initially created around a narrow `BlockCategoryResource` boundary refactor.

Future skills should move toward broader architecture-safe refactoring behavior.

---

## Current / existing backend skills

```text
ws_backend_resource_boundary.md
ws_backend_category_contract.md
ws_backend_eav_mapping.md
ws_backend_repository_loading.md
ws_backend_block_category_guardrails.md
ws_backend_manual_regression.md
```

## Planned skills v2

```text
ws_backend_read_side_refactor.md
ws_backend_contract_preservation.md
ws_backend_resource_boundary.md
ws_backend_repository_and_query_loading.md
ws_backend_eav_mapping.md
ws_backend_assembler_decision.md
ws_backend_seed_pipeline.md
ws_frontend_backend_contract.md
ws_agent_regression_protocol.md
```

## Skills v2 direction

Shift from:

```text
perform this exact patch
```

Toward:

```text
inspect → map → propose → refactor → verify
```

---

# 7. Agent Tasks

## Role

Task files are concrete execution prompts.

They should not be used as architecture documentation.

They should reference relevant info and skills.

---

## Previous narrow task

```text
TASK-BE-001-block-category-resource-boundary-refactor.md
```

Status:

```text
Stopped / considered insufficient as methodological direction.
```

Lesson:

```text
A too-narrow task can produce a local patch while missing the broader read-side design issue.
```

---

## Planned tasks

```text
TASK-BE-002-complex-read-side-refactor.md
TASK-BE-003-optional-seeders-refactor.md
```

`TASK-BE-002` should focus on broad read-side refactor while preserving API compatibility.

`TASK-BE-003` should focus on seed/import pipeline, separately from read-side API refactor.

---

# 8. Review / Runbook Materials

Planned:

```text
METHOD.md
RUNBOOK.md
REVIEW-CHECKLIST.md
```

## `METHOD.md`

Explains the overall context-engineering and agent-safe refactoring method.

## `RUNBOOK.md`

Explains how to launch agents and compare results.

## `REVIEW-CHECKLIST.md`

Explains how to manually review agent output before accepting diffs.

---

# 9. Legacy / Secondary Context

## `.gemini/`

Status:

```text
secondary / legacy context
```

Rule:

```text
.agents materials take priority over .gemini unless a task explicitly says otherwise.
```

## Old generated task files

Some old tasks may remain useful as historical examples, but should not override current direction.

---

# 10. Material Dependency Matrix

## Backend read-side refactor

Read:

```text
BE-00-overview.md
BE-03-eav-domain.md
BE-04-repository-layer.md
BE-05-resource-layer.md
BE-CATEGORY-ENDPOINT-CONTRACT.md
BE-RESOURCE-BOUNDARY.md
BE-02-blocks-read-side-flow.md once created
AIP-BE-001-read-side-refactor.md once created
```

Skills:

```text
ws_backend_read_side_refactor.md
ws_backend_contract_preservation.md
ws_backend_resource_boundary.md
ws_backend_repository_and_query_loading.md
ws_agent_regression_protocol.md
```

---

## Seeder/import refactor

Read:

```text
BE-08-content-seed-pipeline.md once created
AIP-BE-004-seeders-import-pipeline.md once created
```

Skills:

```text
ws_backend_seed_pipeline.md
ws_agent_regression_protocol.md
```

---

## Frontend presentation refactor

Read:

```text
DS.md
TL.md
AL.md
CA.md
DM.md
FE-ARCHITECTURE-SUMMARY.md once created
```

Skills:

```text
frontend skills if/when regenerated
```

---

## API contract changes

Read:

```text
BE-CATEGORY-ENDPOINT-CONTRACT.md
FE-BACKEND-CONTRACT-BRIDGE.md once created
AIP-BE-002-category-response-contract.md once created
AIP-BE-005-naming-and-compatibility-layer.md once created
```

Rule:

```text
No public response shape change without explicit approved task.
```

---

# 11. Current Material Creation Plan

The current accepted phased plan:

```text
Stage 1:
  INDEX.md
  MATERIALS-MAP.md
  GLOSSARY.md

Stage 2A:
  BE-01-routing-and-scope.md
  BE-02-blocks-read-side-flow.md

Stage 2B:
  BE-06-eav-content-resolver.md
  BE-07-block-attach-map.md
  BE-08-content-seed-pipeline.md

Stage 3:
  FE-ARCHITECTURE-SUMMARY.md
  FE-BACKEND-CONTRACT-BRIDGE.md

Stage 4:
  AIP materials

Stage 5:
  Agent runtime files

Stage 6:
  Skills v2

Stage 7:
  Task templates

Stage 8:
  TASK-BE-002 and TASK-BE-003

Stage 9:
  METHOD / RUNBOOK / REVIEW-CHECKLIST
```

---

# 12. Governance Rule

When materials conflict, use this priority:

```text
1. Current user instruction in the active task
2. Current task file
3. AGENTS.md
4. .agents/agents.md
5. .agents/info current architecture docs
6. .agents/skills relevant to task
7. older generated tasks / legacy docs
8. .gemini secondary context
```

When a contradiction is found, the agent must report it instead of silently choosing a convenient interpretation.

