# TASK-BE-004 — Complex Backend Architecture Audit and Next-Task Calibration

## Status

Architecture / audit task.

## Priority

High.

## Purpose

Perform a senior-level backend architecture audit of the current Laravel API codebase after the first two backend refactoring tasks have already been executed.

This task is not a direct refactoring task.
Its job is to inspect the real current code, assess the remaining architectural weaknesses, and calibrate the next implementation task so future agent runs do not continue blindly from outdated assumptions.

## Why this task exists

Earlier backend tasks already started useful work:

```text
TASK-BE-002 — Complex Refactor of Blocks Read-Side Data Loading System
TASK-BE-003 — Optional Refactor of Block Content Seeders and Import Pipeline
```

Observed current state after those tasks:

```text
app/Support/CategoryPayloadAssembler.php exists.
BlockCategoryResource delegates category payload assembly to CategoryPayloadAssembler.
BlockResource now uses explicit public fields instead of blindly exposing attributesToArray().
database/seeders/Helpers/ImportHelper.php exists.
Several seeders were simplified through ImportHelper.
```

This is a meaningful start, but it does not complete the backend architecture work.
The next risk is not just “where to move one method”.
The next risk is that code agents may continue changing Laravel code without a verified map of contracts, tests, route registration, data-loading boundaries, seed/import assumptions, and remaining layer violations.

## Role

Act as a Senior Laravel Architect / Backend Systems Auditor.

Assume the system is a real production-oriented Laravel API backend for a Vue SPA, not a greenfield training project.

Use these lenses:

```text
- Laravel idioms and framework conventions
- SOLID
- Clean Architecture / Ports-and-Adapters thinking where useful
- pragmatic layered architecture
- API contract preservation
- EAV / content-platform architecture
- testability
- refactor safety
- agent-coding readiness
```

Do not pursue theoretical purity when it conflicts with the existing project constraints.
The preferred principle remains:

```text
improve without breaking
```

## Primary inputs

Inspect the real repository, not only this task file.

Primary sources:

```text
AGENTS.md
.agents/agents.md
.agents/README.md
.agents/info/**/*.md
.agents/info/improvements/**/*.md
.agents/skills/**/*.md
.agents/tasks/TASK-BE-002*.md
.agents/tasks/TASK-BE-003*.md
```

Primary backend source areas:

```text
routes/api.php
bootstrap/app.php
app/Providers/AppServiceProvider.php
app/Http/Controllers/Api/*.php
app/Repositories/*.php
app/Http/Resources/*.php
app/Support/*.php
app/Models/*.php
app/Http/Requests/*.php
app/Http/Middleware/*.php
app/Filament/Resources/**/*.php
database/migrations/*.php
database/seeders/**/*.php
config/filesystems.php
config/database.php
config/cors.php
composer.json
phpunit.xml
tests/**/*.php
```

Known reference endpoint:

```text
GET /en/blocks/categories/services
GET /api/en/blocks/categories/services
```

Do not assume which one is correct. Inspect route registration and route:list.

## External / community baseline to consider

Before finalizing the audit recommendations, compare the local project with current Laravel/community practice.

Minimum community baseline:

```text
1. Laravel API Resources are a transformation layer between Eloquent models and JSON responses.
2. Laravel service container / dependency injection should be preferred over hidden construction when dependencies become non-trivial.
3. Laravel HTTP tests provide a first-class way to exercise endpoints and inspect JSON responses.
4. Laravel Pint is the default low-friction code-style safety tool.
5. Larastan / PHPStan is a common optional quality gate for larger Laravel systems, but adding it is a separate dependency decision.
6. In modern Laravel, routes/api.php is normally registered through bootstrap/app.php and receives the API prefix automatically unless configured otherwise.
```

If web access is available, verify these points from current official documentation before writing the final report.
If web access is not available, state that the audit uses local knowledge and mark external verification as not performed.

Do not import random blog advice as authority.
Prefer official Laravel documentation, package documentation, and widely adopted community tools.

## What must be audited

### 1. Route and request lifecycle audit

Map the actual route lifecycle:

```text
HTTP request
  → bootstrap/app.php route registration
  → AppServiceProvider route registration, if any
  → routes/api.php
  → middleware SetLocale
  → controller
  → repository / assembler / resource
  → JSON response
```

Specifically inspect:

```text
- Whether routes/api.php is registered once or twice.
- Whether /api prefix is automatic, manually added, duplicated, or intentionally bypassed.
- Whether route names are stable and usable for tests.
- Whether {locale} is only a locale or also a content scope.
- Whether SetLocale has too broad a responsibility or is acceptable for now.
```

Known issue to verify:

```text
bootstrap/app.php appears to register routes/api.php through withRouting(api: ...).
AppServiceProvider also appears to register routes/api.php manually with prefix('api').
This may create duplicate route registration or hide route-prefix confusion.
Do not change it in this audit task; document and classify the risk.
```

### 2. Read-side architecture audit

Inspect the current state after TASK-BE-002.

Map:

```text
BlockCategoryController
  → BlockCategoryRepository
    → BlocksCategories model graph
      → CategoryPayloadAssembler
        → EavContentResolver
        → BlockAttachMap
          → BlockCategoryResource / BlockResource / BlockItemResource
```

Check:

```text
- Does any Resource still perform SQL or hidden lazy loading?
- Does any Controller still perform query logic that belongs to Repository?
- Does CategoryPayloadAssembler improve clarity or become a new God object?
- Does BlockCategoryRepository return fully prepared graph data?
- Are relation names clear enough: items, propertyValues, properties, children, childrenRecursive?
- Is there duplicate EAV transformation between EavContentResolver and BlockItemResource?
- Is BlockAttachMap still a static compatibility policy or drifting toward hidden business logic?
```

Known issue to verify:

```text
BlockCategoryController::offers() still appears to query BlocksCategories and Block directly inside the controller.
This violates the thin-controller principle used by the rest of the module.
Classify this as a concrete boundary cleanup candidate.
```

### 3. API contract audit

Audit public response contracts for:

```text
GET /{locale}/blocks/categories/{slug}
GET /{locale}/blocks/categories/structure/{slug?}
GET /{locale}/blocks/categories/offers/{slug}
GET /{locale}/blocks/blocks/{slug}
GET /{locale}/blocks/items/{slug}
POST /{locale}/forms/submit
```

For each endpoint, record:

```text
- route name
- controller action
- response wrapper / shape
- Resource used, if any
- known frontend consumer, if visible
- compatibility-sensitive keys
- untested assumptions
```

Critical fields that must not be renamed casually:

```text
data
content
sections
subcategories
children
childs
blocks
items
properties
section
locale
scope
acticle
metadata
priority
```

### 4. Test and regression audit

Inspect:

```text
tests/Feature/ExampleTest.php
tests/Unit/ExampleTest.php
tests/Pest.php
tests/TestCase.php
phpunit.xml
composer.json
```

Determine:

```text
- Which tests are real and which are default skeleton tests.
- Whether RefreshDatabase is enabled globally, per test, or not used.
- Whether SQLite in-memory testing can run the project migrations.
- Whether endpoint contract tests exist.
- Whether EAV transformation tests exist.
- Whether architecture boundary tests exist.
- Whether Pint is installed and usable.
- Whether Larastan/PHPStan is installed or should remain a future proposal.
```

Known issue to verify:

```text
Current tests appear to be default skeleton tests only.
This means backend refactors currently lack executable contract safety.
This is likely the highest-leverage weakness before more structural changes.
```

### 5. Seeder / import audit

Inspect the result after TASK-BE-003:

```text
database/seeders/Helpers/ImportHelper.php
database/seeders/DatabaseSeeder.php
active seeders
legacy seeders
BlockContentHelper
config/filesystems.php
storage/app/blocks assumptions
```

Check:

```text
- Is ImportHelper useful or too static/procedural?
- Does it preserve value_type correctly or collapse non-array types into string?
- Are timestamps handled consistently?
- Are hardcoded IDs still present where risky?
- Are active vs legacy seeders clearly documented?
- Do seeders require files that are absent from repository / ignored storage?
- Can tests rely on seeders, or do they need isolated fixtures?
```

Do not change seeders in this audit task.

### 6. Model and schema audit

Inspect:

```text
Block
BlockItem
BlockItemProperty
BlockItemPropertyValue
BlocksCategories
Form
migrations
SQL dump, if present
```

Evaluate:

```text
- relation naming clarity
- missing return types
- fillable/guarded implications for tests and Filament
- unused pivot table risk
- unique key implications
- missing indexes for locale/version/sort-like queries
- EAV performance risk
- compatibility with SQLite test database
```

Do not propose schema changes as immediate edits unless they are low-risk and separately scoped.
Schema changes should normally become an AIP or future task.

### 7. Filament/admin audit

Inspect Filament Resources at a high level.

Record:

```text
- whether admin resources reflect the same domain model as API
- whether they bypass invariants enforced by seeders/importers
- whether admin changes can break frontend API assumptions
- whether property/value editing requires validation hardening
```

Do not refactor Filament in this audit task.

### 8. Operations and security audit

Inspect:

```text
config/cors.php
.env.example
FormSubmitRequest
FormController
SetLocale
TestMailSend command
```

Record:

```text
- input validation state
- form payload persistence behavior
- email / notification assumptions
- CORS exposure assumptions
- environment safety issues
- public API exposure assumptions
```

Do not introduce auth or security redesign in this task.

## Findings format

Classify every finding with:

```text
ID: BE-AUDIT-###
Severity: P0 / P1 / P2 / P3
Area: routing / read-side / resources / repository / tests / seeders / schema / forms / filament / ops / docs
Evidence: exact files/methods inspected
Risk: what can break
Recommendation: what should happen
Task candidate: TASK-BE-005 / TASK-BE-006 / AIP / postpone
Confidence: high / medium / low
```

Severity guidance:

```text
P0 — currently likely broken or dangerous for production/runtime
P1 — likely to cause regressions or block safe future refactoring
P2 — real maintainability/performance/testability debt
P3 — naming/style/documentation cleanup
```

## Required output files

Create or update:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
.agents/reports/AUDIT-BE-004-findings.json
```

The JSON file may be omitted if the agent platform cannot conveniently create it, but the markdown report is required.

The markdown report must contain:

```text
1. Executive summary
2. Current architecture map
3. What TASK-BE-002 improved
4. What TASK-BE-003 improved
5. Remaining critical weaknesses
6. Community / Laravel baseline considered
7. Findings table
8. Recommended next task calibration
9. Explicit recommendation for TASK-BE-005
10. Backlog candidates for TASK-BE-006+
```

## Allowed changes

Allowed:

```text
- create audit report files under .agents/reports/
- update or annotate the next task file if explicitly part of the run
- add TODO notes inside .agents materials only
```

Not allowed:

```text
- change application source code
- change routes
- change database schema
- change seeders
- change frontend
- run destructive database commands
- install dependencies
- rewrite TASK-BE-002 or TASK-BE-003 history
```

## Expected final report from the agent

At the end of the run, report:

```text
1. Files inspected.
2. Reports created.
3. Top 5 findings.
4. Whether route registration duplication was confirmed.
5. Whether executable contract tests exist.
6. Whether TASK-BE-005 should be modified before execution.
7. Exact recommended next command / task.
```

## Success criteria

This task is successful if:

```text
- the current backend architecture is mapped honestly
- previous work is treated as input, not repeated blindly
- weak points are prioritized
- the next implementation task is better focused
- future code-agent runs have a reliable audit baseline
```

## Failure criteria

This task fails if:

```text
- it changes application code
- it gives generic Laravel advice without inspecting this project
- it ignores TASK-BE-002 / TASK-BE-003 results
- it proposes a rewrite without compatibility strategy
- it does not produce a concrete next-task recommendation
```

## Core reminder

The audit is not meant to prove that the project is bad.
It is meant to make the next engineering step safe.

The correct mindset:

```text
The project has a working content architecture.
TASK-BE-002 and TASK-BE-003 improved it.
Now the system needs an executable safety net and a prioritized backlog before deeper refactoring continues.
```
