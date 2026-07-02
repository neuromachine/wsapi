# METHOD — WebSolutions Agent-Coding Methodology

> Purpose: define the general working method for coding agents operating inside the WebSolutions / WS CODE project.
>
> This document is not a task. It is the stable methodology layer that explains how to read context, how to choose the correct level of intervention, how to protect contracts, and how to report results.

---

## 1. Role of this methodology

The project uses `.agents` as a context-engineering layer for human-guided and ML-assisted code work.

The goal is not to make agents blindly apply patches.

The goal is to make agents operate as careful engineering collaborators:

```text
understand the system
  → identify the actual boundary
    → propose the smallest safe improvement
      → implement only when the task allows it
        → validate behavior
          → report honestly
```

This method is especially important because WebSolutions contains several transitional architectural layers:

```text
Vue SPA frontend
Laravel API backend
EAV content model
block/category read-side API
seed/import pipeline
legacy naming compatibility
future Server-Driven UI direction
```

The project already contains working behavior. The agent must treat that behavior as valuable project knowledge, even where implementation details look imperfect.

---

## 2. Core principle

```text
Improve without breaking.
```

This principle has priority over theoretical cleanliness.

A change is considered good only when it improves structure, clarity, safety, or maintainability while preserving the public contract unless the task explicitly approves a breaking migration.

A change is considered bad when it makes the code look cleaner internally but breaks existing frontend expectations, seeded content, route behavior, locale handling, or response shape.

---

## 3. Context hierarchy

Agents must read context in priority order.

```text
1. Current user instruction / current task request
2. The specific task file in .agents/tasks/
3. Root AGENTS.md
4. .agents/agents.md
5. .agents/info/* relevant architecture documents
6. .agents/info/improvements/* relevant AIP documents
7. .agents/skills/* relevant skill documents
8. .agents/tasks/templates/* if creating or adapting a task
9. Existing walkthroughs, reports, and prior implementation notes
10. Legacy / redundant / external context only after filtering
```

If two documents conflict, the newer and more specific task-level instruction wins, unless it violates explicit compatibility or safety constraints.

---

## 4. Material layers and their meaning

### 4.1 `info/` — current architecture context

`info/` documents describe how the system works or is expected to work.

They are not implementation tasks.

Use them to understand:

```text
- frontend layering
- backend routing and scope
- read-side flow
- EAV model
- resources/repositories boundaries
- seed/import pipeline
- frontend/backend bridge
- glossary and naming debt
```

### 4.2 `info/improvements/` — AIP layer

AIP means Architecture Improvement Proposal.

AIP documents describe possible or recommended architectural directions. They are decision material, not direct edit commands.

An agent may use an AIP to justify a design, but must not treat it as authorization to perform all proposed changes.

### 4.3 `skills/` — reusable execution rules

Skills describe how to perform a class of work.

Examples:

```text
- backend read-side refactor
- contract preservation
- resource boundary
- EAV mapping
- repository/query loading
- assembler decision
- seed pipeline
- frontend/backend contract
- regression protocol
```

Skills are reusable constraints and heuristics. They do not replace the task file.

### 4.4 `tasks/` — executable work

Task files define concrete work.

A task may allow:

```text
- analysis only
- proposal only
- code changes
- tests
- report generation
- modification of a follow-up task
```

The agent must obey the task's allowed and forbidden changes.

### 4.5 `tasks/templates/` — reusable task forms

Templates are used by humans or agents when creating new task files.

Do not execute a template directly unless a task explicitly says to instantiate it.

---

## 5. Standard agent workflow

Every non-trivial work session should follow this sequence.

### Stage 1 — Inspect

Read the task and relevant context before editing.

Identify:

```text
- target files
- public contracts
- hidden assumptions
- existing tests
- previous walkthroughs
- known compatibility debt
```

Do not start by editing the file that looks most obviously wrong.

### Stage 2 — Map

Build a simple flow map.

For backend read-side work, map:

```text
route
  → controller
    → repository/query
      → model relations
        → support transformers
          → resource
            → JSON response
```

For frontend work, map:

```text
route
  → view
    → index/orchestrator
      → store/composable
        → presentation component
          → UI primitive
```

For seed/import work, map:

```text
content source
  → JSON reader/helper
    → seeder
      → upsert helper
        → database rows
          → API read-side output
```

### Stage 3 — Identify contracts

Before changing code, identify what must not break.

Contracts may include:

```text
- endpoint URL
- Laravel Resource envelope: response.data.data
- JSON keys
- frontend props
- legacy field names
- locale/scope behavior
- seeded content keys
- database schema
- route names
- component layering rules
```

### Stage 4 — Decide level of intervention

Prefer the smallest level that solves the real problem.

Possible levels:

```text
1. Documentation/report only
2. Test/safety-net addition
3. Local cleanup
4. Boundary cleanup
5. Extract helper/collaborator
6. Refactor flow
7. Migration / breaking change proposal
```

Do not jump to level 6 or 7 when level 2 or 3 is the safer next move.

### Stage 5 — Propose or implement

If the task requires a proposal, do not edit production code.

If the task allows implementation, keep the change coherent and bounded.

Good implementation traits:

```text
- small named collaborators
- explicit contracts
- low surprise
- compatibility aliases instead of breaking renames
- tests before broad refactor when possible
- clear fallback behavior
```

Bad implementation traits:

```text
- broad rewrite
- framework-style abstraction without need
- renaming public keys casually
- moving logic without improving responsibility boundaries
- turning helpers into God objects
- touching unrelated layers
```

### Stage 6 — Verify

Run available checks when possible.

Expected checks may include:

```text
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
php -l changed_file.php
npm run test:run
npm run build
manual endpoint comparison
contract diff against reference payload
```

If a command cannot be run, state exactly why.

Never claim runtime validation if only static review was performed.

### Stage 7 — Report

Final reports must be specific.

Include:

```text
- files inspected
- files changed
- why changes were made
- public contracts preserved
- tests/checks run
- checks not run and why
- remaining risks
- recommended next step
```

Avoid vague claims like "everything is fixed" or "fully optimized".

---

## 6. Backend methodology

### 6.1 Backend architectural target

The backend should move toward this practical layered shape:

```text
Controller
  → Repository / Query Layer
    → Prepared model graph
      → Optional Payload Builder / Assembler
        → Resource serialization
          → JSON response
```

This is not a strict Clean Architecture rewrite. It is a compatibility-preserving layered architecture adapted to the existing Laravel project.

### 6.2 Controller rule

Controllers should be thin.

Allowed:

```text
- receive route params
- call repository/service/use-case
- return resource/response
```

Avoid:

```text
- direct Eloquent query construction
- EAV transformation
- category response assembly
- hidden business decisions
```

If a controller currently contains such logic, do not automatically rewrite it. First cover behavior with tests or document the risk, then move logic in a bounded task.

### 6.3 Repository rule

Repository prepares data.

Allowed:

```text
- Eloquent queries
- eager loading
- locale filtering
- recursive relation loading
- relation completeness
- low-level sorting when appropriate
```

Avoid:

```text
- formatting public JSON
- frontend naming decisions
- becoming a large God object
```

If repository logic grows too complex, prefer a small named query object or assembler collaborator.

### 6.4 Resource rule

Resource serializes prepared data.

Allowed:

```text
- explicit field output
- null-safe formatting
- calling pure transformers on loaded data
- preserving compatibility keys
```

Avoid:

```text
- SQL queries
- Model::where()
- Repository calls
- hidden eager loading
- business filtering
- response-shape changes without task approval
```

### 6.5 EAV rule

EAV is a storage strategy, not a public API shape.

Public API should receive logical flat objects, not raw property/value internals.

`EavContentResolver` should remain focused on:

```text
Collection<BlockItem>
  → flat object / keyed object / array of objects
```

It must not become a query service, category assembler, or route-aware component.

### 6.6 Assembler rule

Assembler/PayloadBuilder is appropriate when response assembly is larger than serialization but should not live in Repository.

Good assembler:

```text
- receives prepared models/collections
- performs deterministic mapping
- has no SQL
- has a narrow response target
- is easy to test
```

Bad assembler:

```text
- performs queries
- knows every endpoint
- mixes seed/import/read-side concerns
- hides compatibility decisions without tests
```

---

## 7. Frontend methodology

Frontend changes should respect the current component hierarchy.

```text
View.vue
  → index.vue / orchestrator
    → presentation list/section components
      → item components
        → UI primitives
```

Rules:

```text
- View.vue composes only
- index.vue owns store/composable/fetch orchestration
- presentation components receive props
- UI primitives are generic and business-agnostic
- AppLink should protect scoped navigation
- frontend expects Laravel Resource envelope: response.data.data
```

Do not compensate for backend response instability by spreading ad-hoc normalization through many components.

Prefer one bridge/normalization boundary when possible.

---

## 8. Contract preservation method

When a public contract contains imperfect names, preserve them until a migration task exists.

Known compatibility debt includes:

```text
childs / children / subcategories
acticle / article
items / items.items ambiguity
locale / scope / section
section as route/backend field
legacy HTML content fragments
```

Safe strategy:

```text
1. Keep current public key.
2. Add internal clearer naming if useful.
3. Optionally expose alias only when approved.
4. Add tests around both old and new behavior.
5. Migrate frontend in a separate task.
6. Remove legacy only after explicit deprecation window.
```

---

## 9. Testing and regression philosophy

For this project, regression safety is not optional.

The system contains dynamic EAV data, seeded content, and frontend-dependent response shapes. Therefore, "it looks cleaner" is not enough.

Preferred safety net order:

```text
1. Contract tests for public endpoints
2. Unit tests for pure transformers/helpers
3. Route registration tests for API prefix behavior
4. Seeder/import tests where feasible
5. Static/style checks
6. Manual endpoint diff against reference payloads
```

For backend read-side, the first reference endpoint remains:

```text
GET /en/blocks/categories/services
```

For frontend bridge, the first consumed route remains:

```text
/en/services
```

---

## 10. How to create new tasks

When creating a new task, use the templates in:

```text
.agents/tasks/templates/
```

A good task must include:

```text
- status and priority
- relation to previous tasks
- context
- main goal
- primary target area
- what must be investigated
- allowed changes
- forbidden changes
- strategy
- regression checklist
- expected deliverables
- success/failure criteria
```

A task must not prescribe a premature solution when the correct solution depends on source inspection.

Prefer:

```text
Inspect and choose the smallest safe design.
```

Avoid:

```text
Move method X to file Y because we assume that is enough.
```

---

## 11. Handling community / external recommendations

External recommendations are useful, but must be adapted to this project.

Use Laravel/community practice to calibrate decisions around:

```text
- API Resources as transformation layer
- feature/HTTP tests for API endpoints
- Pest/PHPUnit for backend regression
- Pint for formatting
- dependency injection through container
- avoiding fat controllers
- service/repository boundaries only when they add clarity
- static analysis as future quality gate
```

Do not import community patterns blindly.

For example:

```text
- Do not introduce a large service layer only because a blog recommends it.
- Do not migrate all data to DTOs before contract tests exist.
- Do not install new packages unless a task explicitly allows dependencies.
- Do not turn Laravel into a theoretical Clean Architecture sample app.
```

Use community practice as a compass, not as a replacement for project context.

---

## 12. Definition of done

A task is done when:

```text
- the requested work was completed within scope
- public contracts were protected or changes were explicitly justified
- validation was performed or honestly marked as not performed
- remaining risks are documented
- the next step is clear
```

A task is not done when:

```text
- code changed but no behavior was checked
- output shape changed silently
- the agent claims success without evidence
- unrelated files were rewritten
- implementation created more ambiguity than it removed
```

---

## 13. Core reminder

WebSolutions is not a greenfield code kata.

It is a living project with working frontend behavior, backend content contracts, seeded data, and ongoing migration toward cleaner architecture.

The correct engineering posture is:

```text
careful
contract-aware
incremental
explicit
honest
```
