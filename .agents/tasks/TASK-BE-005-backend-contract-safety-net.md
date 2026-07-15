# TASK-BE-005 — Backend Contract Safety Net

## Status

Immediate next backend task after `TASK-BE-004`.

## Type

Testing / contract safety net / regression foundation.

## Priority

P0 — must be completed before further backend structural refactoring.

## Core Principle

Write the safety net before continuing architecture changes.

Do not refactor production architecture in this task.
Do not fix `BlockCategoryResource::attributesToArray()` yet.
Do not refactor `BlockCategoryController::offers()` yet.
Do not remove duplicate EAV model relationships yet.

This task exists to make later refactoring safe.

---

## Source Context

This task is based on the completed report:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

The audit identified these relevant risks:

```text
P0 — zero API contract tests
P1 — duplicated route registration / API bootstrap uncertainty
P1 — BlockCategoryResource uses attributesToArray()
P2 — BlockCategoryController::offers() bypasses Repository/Resource boundaries
P2 — BlockItem has duplicate property value relations
```

`TASK-BE-004` explicitly recommends that structural changes be halted until this safety net is built.

---

## Main Goal

Create automated backend tests that lock down the current public API contracts for the Blocks/EAV read-side before any further refactoring.

The tests must make the following future changes safer:

```text
- route/bootstrap cleanup
- offers endpoint boundary refactor
- explicit Resource serialization hardening
- EAV relationship cleanup
- later frontend contract handoff
```

---

## Primary Contract Targets

### 1. Standard category endpoint

Control endpoint:

```text
GET /en/blocks/categories/services
```

This is the main Vue-facing Services page endpoint.

It currently follows Laravel Resource envelope conventions:

```text
response.data.data
```

The test must lock down the presence and general shape of:

```text
json root:
  data

inside data:
  id
  key
  name
  description
  content
  parent_id
  created_at
  updated_at
  section
  sections
  subcategories
  blocks
  children
```

The test must also assert that legacy/current compatibility keys are preserved:

```text
section
sections
content
subcategories
children
childs inside subcategories[]
```

Do not rename these keys in this task.

### 2. Non-standard offers endpoint

Control endpoint pattern:

```text
GET /en/blocks/categories/offers/{slug}
```

This endpoint is intentionally included because the audit found that:

```text
- it returns a flat JSON object
- it does not use the standard Laravel Resource .data envelope
- it is assembled manually inside BlockCategoryController::offers()
```

Before later refactoring, tests must lock down its current shape.

Expected top-level shape:

```text
category
block
items
```

Expected explicit non-envelope assertion:

```text
There should be no required top-level data envelope for the current offers endpoint.
```

If the frontend later agrees to migrate this endpoint to a `.data` envelope, that must be a separate contract migration task with frontend handoff.

---

## Secondary Unit / Support Targets

Add targeted tests for pure transformation and policy classes where feasible:

```text
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
```

Recommended unit coverage for `EavContentResolver`:

```text
- single item mode
- keyed mode
- array mode
- string values
- json values
- integer / float / number values
- boolean values
- is_collection values
- sorting by sort / priority-like field if current resolver supports it
```

Recommended coverage for `BlockAttachMap`:

```text
- known attach target for descr_data
- known attach target for slide/list/simplehtml/works if present
- isSingle()
- isKeyed()
- unknown block key behavior
```

Do not turn these support classes into services in this task.

---

## Files to Inspect First

Before writing tests, inspect:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
.agents/METHOD.md
.agents/RUNBOOK.md
.agents/REVIEW-CHECKLIST.md
.routes/api.php
.bootstrap/app.php
.app/Providers/AppServiceProvider.php
.app/Http/Controllers/Api/BlockCategoryController.php
.app/Http/Resources/BlockCategoryResource.php
.app/Http/Resources/BlockResource.php
.app/Http/Resources/BlockItemResource.php
.app/Support/CategoryPayloadAssembler.php
.app/Support/EavContentResolver.php
.app/Support/BlockAttachMap.php
.tests/Feature
.tests/Unit
.phpunit.xml
.composer.json
```

---

## Allowed Changes

You may create or modify test-related files, such as:

```text
tests/Feature/BlockCategoryServicesContractTest.php
tests/Feature/BlockCategoryOffersContractTest.php
tests/Unit/EavContentResolverTest.php
tests/Unit/BlockAttachMapTest.php
tests/Support/*
tests/Fixtures/*
tests/Pest.php
tests/TestCase.php
phpunit.xml only if strictly necessary and safe
```

You may add small test-only fixture builders if needed.

You may create the final report:

```text
.agents/reports/REPORT-BE-005-backend-contract-safety-net.md
```

---

## Forbidden Changes

Do not modify production architecture files in this task unless there is a tiny, strictly necessary test-enablement fix and it is clearly justified.

Specifically do not change:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Http/Resources/BlockCategoryResource.php
app/Repositories/BlockCategoryRepository.php
app/Support/CategoryPayloadAssembler.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Models/BlockItem.php
routes/api.php
bootstrap/app.php
app/Providers/AppServiceProvider.php
database/migrations/**
database/seeders/**
frontend files
```

Do not:

```text
- fix route duplication yet
- refactor offers() yet
- remove attributesToArray() yet
- consolidate properties/propertyValues relations yet
- migrate BlockAttachMap into DB metadata yet
- change public JSON keys
- change endpoint URLs
- change frontend code
```

These belong to later tasks.

---

## Test Data Strategy

Prefer deterministic isolated test data.

Recommended order:

### Option A — Isolated test fixtures, preferred

Use migrations/RefreshDatabase and create the minimum EAV graph required for the tests:

```text
blocks_categories:
  services
  one or more service subcategories
  one category suitable for offers/{slug}

blocks:
  descr_data
  offers
  any block required for sections if testing it

block_items:
  category-bound items

block_item_properties:
  title
  descr
  content
  metadata
  priority
  other minimal required properties

block_item_property_values:
  localized values for en
```

### Option B — Existing seeded database smoke test, temporary

Only use the existing local seeded DB if isolated fixtures are impractical in the current environment.

If using this option, state clearly in the report:

```text
This is a smoke/contract test over existing seeded data, not a fully isolated test.
```

### Option C — Static test file generation only, fallback

If PHP/Composer are not available in the agent execution environment, create the tests statically and report that they must be run manually by the human developer.

This is partial completion only.

---

## Expected Tests

At minimum, create:

```text
tests/Feature/BlockCategoryServicesContractTest.php
tests/Feature/BlockCategoryOffersContractTest.php
```

Strongly recommended:

```text
tests/Unit/EavContentResolverTest.php
tests/Unit/BlockAttachMapTest.php
```

Optional:

```text
tests/Feature/RouteRegistrationContractTest.php
```

The optional route registration test may document the current duplicated route risk, but it must not fix it. The actual cleanup belongs to `TASK-BE-006`.

---

## Required Assertions

### Services endpoint

The services endpoint test must assert:

```text
- HTTP 200 for GET /en/blocks/categories/services
- root has data
- data.key === services if test fixture supports this exact assertion
- data.section === en
- data.content exists and is object/array
- data.sections exists
- data.subcategories exists and is array
- each relevant subcategory contains id, slug, childs
- data.blocks exists
- data.children exists or remains present as current compatibility field
- no raw EAV internals are required by frontend assertions
```

### Offers endpoint

The offers endpoint test must assert:

```text
- HTTP 200 for GET /en/blocks/categories/offers/{slug}
- response has category
- response has block
- response has items
- response does not require top-level data envelope in the current contract
- items[] contains id, key, name, properties where test data supports it
```

### Resolver / attach map

Unit tests must assert existing behavior, not desired future behavior.

---

## Commands to Run

Run as many as the environment allows:

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
php artisan route:list
composer validate
```

If using Windows PowerShell:

```powershell
php artisan test
.\vendor\bin\pest
.\vendor\bin\pint --test
php artisan route:list
composer validate
```

If PHP or Composer are unavailable, do not claim runtime validation succeeded.

---

## Expected Final Report

Create:

```text
.agents/reports/REPORT-BE-005-backend-contract-safety-net.md
```

The report must include:

```text
1. Files inspected.
2. Files created/changed.
3. Test strategy used: isolated fixtures / seeded DB smoke / static-only fallback.
4. Contract endpoints covered.
5. Exact assertions covered.
6. Commands run and results.
7. Commands that could not run and why.
8. Whether BE-006/007/008 are now safe to run.
9. Risks or manual follow-up.
```

---

## Success Criteria

This task is successful if:

```text
- services category contract is covered by feature tests
- offers endpoint contract is covered by feature tests
- legacy keys are explicitly protected
- EavContentResolver and BlockAttachMap have at least minimal unit coverage or a clear reason they could not be covered
- test commands are run, or inability to run is honestly reported
- no production architecture refactor is performed
- a BE-005 report is created
```

---

## Failure Criteria

This task fails if:

```text
- production architecture is refactored before tests exist
- offers endpoint is changed before being locked down
- services response keys are renamed
- tests depend on vague assumptions without fixtures or documented seeded data
- report claims tests passed when they were not executed
- frontend code is changed
```

---

## Next Tasks After Completion

After BE-005 is complete and reviewed, continue with:

```text
TASK-BE-006 — Route and API Bootstrap Cleanup
TASK-BE-007 — Offers Endpoint Boundary Refactor
TASK-BE-008 — Explicit Resource Serialization Hardening
```

Do not run those tasks before BE-005 is reviewed.

