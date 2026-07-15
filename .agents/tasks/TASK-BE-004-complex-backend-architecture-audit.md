# TASK-BE-004 — Complex Backend Architecture Audit

## Status

Execution task / read-only architecture audit.

## Priority

High.

## Core Principle

Audit first. Do not refactor yet.

This task must produce a detailed architecture audit report for the current Laravel backend after the already-started backend refactoring work from TASK-BE-002 and TASK-BE-003.

The agent must **not change application code** during this task.

Allowed write target:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

No other source files should be modified unless the user explicitly approves a follow-up implementation task.

---

## Context

The repository is a Laravel API backend for the WebSolutions / WS CODE platform.

The system contains:

```text
- Laravel REST API backend
- Blocks / Categories / Items / EAV content model
- File-based seed/import pipeline
- Vue SPA frontend consuming Laravel Resource envelopes
- .agents context system created for AI coding agents
```

Previous work has already started:

```text
TASK-BE-002
  Complex read-side refactor around Blocks/Categories.
  Expected result: CategoryPayloadAssembler and cleaner Resource boundary.

TASK-BE-003
  Optional seed/import refactor.
  Expected result: ImportHelper and reduced seeder duplication.
```

Treat TASK-BE-002 and TASK-BE-003 as **starting potential**, not as final architecture.

The goal of this task is to understand the real current state after those changes and identify the safest next architectural work.

---

## Required Inputs

Before auditing, inspect the available project materials.

### Agent runtime and methodology

```text
AGENTS.md
.agents/README.md
.agents/agents.md
.agents/METHOD.md
.agents/RUNBOOK.md
.agents/REVIEW-CHECKLIST.md
```

### Information layer

```text
.agents/info/INDEX.md
.agents/info/MATERIALS-MAP.md
.agents/info/GLOSSARY.md
.agents/info/BE-00-overview.md
.agents/info/BE-01-routing-and-scope.md
.agents/info/BE-02-blocks-read-side-flow.md
.agents/info/BE-03-eav-domain.md
.agents/info/BE-04-repository-layer.md
.agents/info/BE-05-resource-layer.md
.agents/info/BE-06-eav-content-resolver.md
.agents/info/BE-07-block-attach-map.md
.agents/info/BE-08-content-seed-pipeline.md
.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md
.agents/info/BE-RESOURCE-BOUNDARY.md
.agents/info/FE-BACKEND-CONTRACT-BRIDGE.md
```

### AIP layer

```text
.agents/info/improvements/AIP-000-index.md
.agents/info/improvements/AIP-BE-001-read-side-refactor.md
.agents/info/improvements/AIP-BE-002-category-response-contract.md
.agents/info/improvements/AIP-BE-003-resource-to-assembler-boundary.md
.agents/info/improvements/AIP-BE-004-seeders-import-pipeline.md
.agents/info/improvements/AIP-BE-005-naming-and-compatibility-layer.md
.agents/info/improvements/AIP-FE-001-server-driven-ui-path.md
```

### Skills

```text
.agents/skills/ws_backend_read_side_refactor.md
.agents/skills/ws_backend_contract_preservation.md
.agents/skills/ws_backend_resource_boundary.md
.agents/skills/ws_backend_repository_and_query_loading.md
.agents/skills/ws_backend_eav_mapping.md
.agents/skills/ws_backend_assembler_decision.md
.agents/skills/ws_backend_seed_pipeline.md
.agents/skills/ws_frontend_backend_contract.md
.agents/skills/ws_agent_regression_protocol.md
```

### Previous tasks and results

Inspect if present:

```text
.agents/tasks/TASK-BE-002*
.agents/tasks/TASK-BE-003*
implementation_plan_t2.md
implementation_plan_t3.md
walkthrough_t2.md
walkthrough_t3.md
```

The exact filenames may differ. Do not fail if some documents are absent; note missing context in the report.

---

## Primary Audit Scope

Audit the current backend repository as a senior Laravel architect.

Focus on architectural weaknesses that affect the whole project, not just one endpoint.

### 1. Routing and bootstrap

Inspect:

```text
bootstrap/app.php
app/Providers/AppServiceProvider.php
routes/api.php
routes/web.php
app/Http/Middleware/SetLocale.php
```

Determine:

```text
- how API routes are actually registered
- whether routes/api.php is registered once or twice
- whether /api prefix behavior is correct
- how {locale} works
- whether locale/scope/app-locale roles are mixed safely or dangerously
```

### 2. Controller boundaries

Inspect:

```text
app/Http/Controllers/Api/*Controller.php
```

Find:

```text
- direct model queries in controllers
- response assembly inside controllers
- duplicated endpoint logic
- controllers that bypass repositories/resources
```

Controllers should stay thin.

### 3. Repository and query layer

Inspect:

```text
app/Repositories/*.php
```

Evaluate:

```text
- eager loading completeness
- locale filtering strategy
- recursive category handling
- query duplication
- places where repositories are becoming God objects
- missing named query/read collaborators
```

### 4. Resource and serialization layer

Inspect:

```text
app/Http/Resources/*.php
```

Evaluate:

```text
- whether Resources perform SQL
- whether Resources hide business/query decisions
- attributesToArray leakage risk
- explicit vs implicit public fields
- compatibility with Laravel Resource envelope response.data.data
```

### 5. Read-side assembly

Inspect:

```text
app/Support/CategoryPayloadAssembler.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
```

Evaluate:

```text
- whether CategoryPayloadAssembler improved boundaries after TASK-BE-002
- whether it is still small and focused
- whether EAV transformation is consistent across usages
- whether BlockAttachMap is a compatibility policy or hidden architecture debt
- whether content / sections / blocks / subcategories routing is understandable
```

### 6. EAV model consistency

Inspect:

```text
app/Models/Block.php
app/Models/BlockItem.php
app/Models/BlockItemProperty.php
app/Models/BlockItemPropertyValue.php
app/Models/BlocksCategories.php
```

Evaluate:

```text
- relationship naming consistency
- fillable/casts usage
- property.type vs value.value_type tension
- is_collection behavior
- version/draft-publish readiness
- hidden assumptions about unique keys
```

### 7. Seed/import pipeline

Inspect:

```text
database/seeders/**/*.php
database/seeders/Helpers/BlockContentHelper.php
database/seeders/Helpers/ImportHelper.php
config/filesystems.php
storage/app/blocks/**  (if present)
```

Evaluate:

```text
- whether TASK-BE-003 improved duplication safely
- active vs legacy seeders
- hardcoded IDs vs keys
- dependency order
- idempotency
- missing validation
- content drift risk
- JSON/encoding/data-quality risks
```

### 8. Forms subsystem

Inspect:

```text
app/Http/Controllers/Api/FormController.php
app/Http/Requests/FormSubmitRequest.php
app/Models/Form.php
app/Enums/FormStatus.php
lang/*/forms.php
```

Evaluate:

```text
- validation structure
- locale behavior
- persistence model
- spam/rate-limit exposure
- notification/queue readiness
- error response compatibility with frontend
```

### 9. Filament/admin layer

Inspect if present:

```text
app/Filament/Resources/**
```

Evaluate:

```text
- whether admin editing respects the same model constraints as seed/import/API
- risk of invalid EAV data through admin forms
- missing validation/casts
- whether content editors can break API contracts accidentally
```

### 10. Tests, CI and tooling

Inspect:

```text
composer.json
phpunit.xml
tests/**
.github/**  (if present)
```

Evaluate:

```text
- Pest/PHPUnit availability
- current test coverage reality
- absence/presence of API contract tests
- Pint availability
- Larastan/PHPStan readiness as future step, not mandatory install
- suitability of TASK-BE-005 as safety-net task
```

### 11. Frontend contract impact

The backend audit must explicitly identify areas that may require frontend follow-up.

Look for backend behavior that affects:

```text
- response.data.data envelope
- /{locale}/blocks/categories/{slug}
- /{locale}/blocks/blocks/navigation
- forms/submit
- content / sections / blocks / subcategories / children / childs
- item.properties shape
- locale / scope / section semantics
```

Do not edit frontend code in this task. Only produce backend-to-frontend handoff recommendations.

---

## External / Community Practice Calibration

Before finalizing recommendations, calibrate the audit against modern Laravel and clean architecture practice.

Use this as reasoning context, not as a reason to blindly copy patterns:

```text
- Laravel Resources should transform models/resources into JSON responses, not hide queries.
- Laravel HTTP tests can validate JSON API contracts at endpoint level.
- Laravel Pint can provide low-friction style consistency.
- Laravel service container / dependency injection should be used where it improves testability.
- Static analysis such as Larastan/PHPStan can be a later quality gate, but should not be introduced as noise before basic tests exist.
- SOLID/Clean Architecture should guide boundaries, but must not lead to framework-hostile overengineering.
```

If web access is not available, rely on official docs already known to the environment and note that external check could not be performed.

---

## Output Report

Create exactly this file:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

Use this structure:

```text
# AUDIT-BE-004 — Backend Architecture Audit

## 1. Executive Summary
## 2. Repository Snapshot
## 3. What TASK-BE-002 Changed / Current Read-Side State
## 4. What TASK-BE-003 Changed / Current Seed Pipeline State
## 5. Findings by Severity
### P0 — Critical / Must Fix Before Further Work
### P1 — High / Next Refactor Candidates
### P2 — Medium / Important Architecture Debt
### P3 — Low / Cleanup / Documentation
## 6. Layer-by-Layer Audit
### Routing / Bootstrap
### Controllers
### Repositories
### Resources
### CategoryPayloadAssembler
### EavContentResolver
### BlockAttachMap
### EAV Models
### Seeders / Import
### Forms
### Filament / Admin
### Tests / CI / Tooling
### Frontend Contract Impact
## 7. Contract Risks
## 8. Data Quality Risks
## 9. Recommended Next Tasks
## 10. Recommended Modification of TASK-BE-005
## 11. Backend → Frontend Handoff Recommendations
## 12. What Not To Refactor Yet
## 13. Open Questions for Human Review
## 14. Commands Run / Commands Not Run
## 15. Final Recommendation
```

---

## Required Recommendation Format

In section `9. Recommended Next Tasks`, propose concrete future tasks using this format:

```text
TASK-BE-006 — <name>
Priority: P0/P1/P2/P3
Type: audit/refactor/test/contract/data/ci
Depends on: <task id or none>
Reason:
Expected output:
Frontend impact: none / handoff required / frontend task required
```

At minimum consider whether these tasks are needed:

```text
TASK-BE-006 — Route and API Bootstrap Audit / Cleanup
TASK-BE-007 — Offers Endpoint Boundary Refactor
TASK-BE-008 — Explicit Resource Serialization Hardening
TASK-BE-009 — EavContentResolver Test Matrix
TASK-BE-010 — EAV Mapping Consistency: Resource vs Resolver
TASK-BE-011 — BlockAttachMap Compatibility Policy and Migration Path
TASK-BE-012 — Seeder Output Snapshot / Data Drift Safety Net
TASK-BE-013 — Content Integrity Audit
TASK-BE-014 — Backend CI Quality Gate
TASK-FE-001 — API Contract Adapter Layer
TASK-FE-002 — Services Fixture Contract Test
```

Do not create these task files in this task. Only recommend and prioritize them.

---

## Forbidden Changes

Do not:

```text
- modify app/ code
- modify database migrations
- modify seeders
- modify frontend code
- rename public API keys
- change routes
- run destructive commands
- install new dependencies
- generate unrelated documentation
- create TASK-BE-006+ files yet
```

Allowed:

```text
- read files
- run safe inspection commands
- run tests if available and non-destructive
- create .agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

---

## Safe Commands

The agent may run these if available:

```bash
php artisan route:list
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
composer validate
```

If the local environment is incomplete, do not invent results. Record exact failures.

Do not run:

```bash
php artisan migrate:fresh
php artisan db:seed
php artisan migrate:fresh --seed
composer update
npm install
```

---

## Success Criteria

This task succeeds if:

```text
- no application code is changed
- one audit report is created in .agents/reports/
- the report reflects the actual current repository, not old assumptions
- TASK-BE-002 and TASK-BE-003 are evaluated as already-started work
- the next backend tasks are prioritized
- frontend handoff needs are identified
- TASK-BE-005 is either confirmed or adjusted based on findings
```

---

## Failure Criteria

This task fails if:

```text
- the agent starts refactoring code
- recommendations ignore existing .agents materials
- the audit only repeats generic Laravel advice
- the audit does not inspect current files
- no severity classification is provided
- frontend contract impact is omitted
- TASK-BE-005 is treated as final instead of safety-net foundation
```

---

## Core Reminder

The current goal is not to make the backend perfect.

The current goal is to create a reliable architectural map for the next controlled cycle:

```text
audit → safety net → bounded refactor → contract handoff → frontend task → regression
```
