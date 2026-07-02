# TASK-BE-005 — Backend Contract Safety Net and Layer Boundary Hardening

## Status

Implementation task.

## Priority

High.

## Relation to previous tasks

This task is based on the architecture state after:

```text
TASK-BE-002 — read-side refactor
TASK-BE-003 — seed/import refactor
TASK-BE-004 — backend audit / calibration
```

If TASK-BE-004 has not been executed yet, run this task with extra caution and treat its first stage as a mini-audit.

## Main conclusion behind this task

The project has already started moving in the right architectural direction:

```text
- CategoryPayloadAssembler was introduced.
- BlockCategoryResource became thinner.
- BlockResource became more explicit.
- ImportHelper reduced duplicated seeder code.
```

The next highest-leverage weakness is not another broad refactor.
The next highest-leverage weakness is the lack of executable regression protection around the backend API contracts and the remaining visible layer-boundary violations.

Before deeper backend restructuring, add a contract safety net that future code agents can run.

## Core principle

```text
tests and verifiable contracts before more architecture changes
```

Do not continue refactoring the content platform without tests that protect the current API response shape.

## Goals

### Primary goals

```text
1. Add executable backend tests for the critical Blocks API contract.
2. Add focused unit tests for support-layer behavior: EavContentResolver and BlockAttachMap.
3. Add minimal architecture boundary checks so Resources and Controllers do not drift back into hidden SQL/query logic.
4. Make test fixtures independent from production seeders where possible.
5. Confirm actual API route prefix behavior through route-list or tests.
```

### Secondary goal

If and only if the safety net is in place and passing, perform one small boundary hardening:

```text
Move BlockCategoryController::offers() query logic into BlockCategoryRepository or a small dedicated read-side collaborator.
```

This secondary goal may be postponed if it increases risk.

## Why this task is needed

Current observed weaknesses:

```text
- tests/Feature/ExampleTest.php appears to be a default skeleton test.
- tests/Unit/ExampleTest.php appears to be a default skeleton test.
- tests/Pest.php appears to have RefreshDatabase commented out globally.
- composer.json includes Pest and Laravel Pint, but the project does not yet appear to use them as real backend quality gates.
- No contract test currently protects GET /en/blocks/categories/services or GET /api/en/blocks/categories/services.
- BlockCategoryController::offers() appears to perform direct queries in the controller.
- Route registration may be duplicated through both bootstrap/app.php and AppServiceProvider.
```

Do not treat these as accusations.
Treat them as audit hypotheses to verify in the working repository.

## Community / Laravel baseline

Use current Laravel practice as a reference point:

```text
- API Resources should transform models and relationships into JSON, not compensate for missing data preparation.
- The service container / dependency injection should be used for non-trivial dependencies and testability.
- Laravel HTTP tests are the normal way to test endpoints and JSON responses.
- Laravel Pint should be used as a low-friction code-style check.
- Larastan/PHPStan is useful for larger Laravel projects, but dependency installation should be a separate approved task unless already present.
```

Do not add new third-party dependencies in this task unless explicitly approved.

## Primary target files

Testing files likely to create:

```text
tests/Feature/Blocks/BlockCategoryEndpointTest.php
tests/Feature/Blocks/RouteRegistrationTest.php
tests/Unit/Support/EavContentResolverTest.php
tests/Unit/Support/BlockAttachMapTest.php
tests/Unit/Architecture/LayerBoundaryTest.php
tests/Support/BlocksTestData.php
```

Application files that may be inspected:

```text
routes/api.php
bootstrap/app.php
app/Providers/AppServiceProvider.php
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
app/Support/CategoryPayloadAssembler.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Models/*.php
```

Application files that may be changed only if justified:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
composer.json
pint.json
```

Changing `composer.json` is allowed only for scripts using already installed tools.
Do not add Larastan/PHPStan in this task unless it is already installed.

## Stage 1 — Inspect and confirm route behavior

Before writing tests, inspect:

```text
bootstrap/app.php
app/Providers/AppServiceProvider.php
routes/api.php
php artisan route:list
```

Answer these questions:

```text
1. Is routes/api.php registered once or twice?
2. Is the working endpoint /api/en/blocks/categories/services or /en/blocks/categories/services?
3. Are named routes usable for tests?
4. Is the route group middleware correct?
5. Does SetLocale run for all API routes?
```

If route duplication is confirmed, do not automatically remove it unless a failing test or route-list evidence makes the fix low-risk.
Prefer:

```text
- document the finding
- add a test that protects the expected route
- recommend a separate route-cleanup task if necessary
```

## Stage 2 — Build isolated test fixture

Do not rely blindly on production seeders for contract tests.
Production seeders may depend on JSON files, storage paths, execution order, or content history.

Create a minimal test-data helper, for example:

```text
tests/Support/BlocksTestData.php
```

The helper should create the minimum EAV graph needed to exercise the category endpoint:

```text
blocks_categories:
  services
  prodvizenie child of services

blocks:
  descr_data
  list or works, only if needed for sections behavior

block_items:
  services content item
  prodvizenie content item

block_item_properties:
  title
  descr
  content
  metadata
  priority
  sort, if needed

block_item_property_values:
  locale = en
  value_type = string / json / number
```

Use direct DB inserts or model forceFill where safer.
Do not depend on mass assignment unless models explicitly allow it.

The fixture must be small, readable, and local to tests.

## Stage 3 — Add endpoint contract tests

Create a feature test for the category endpoint.

The test should verify:

```text
- endpoint returns 200
- response uses Laravel Resource envelope: data
- data.key = services
- data.section = en, unless route behavior proves otherwise
- data.content exists
- data.content.title exists
- data.subcategories exists as an array
- first subcategory includes id, slug, childs
- first subcategory includes EAV fields: title, descr, content, metadata, priority
- data.blocks exists
- data.sections exists
- data.children exists or remains compatible with current behavior
```

Do not assert the full long HTML content.
Assert structure and stable critical values.

Prefer Laravel JSON assertions such as:

```text
assertOk()
assertJsonPath()
assertJsonStructure()
```

If the correct URL is uncertain, first use named routes or route-list evidence.
Do not hardcode a wrong prefix.

## Stage 4 — Add support-layer unit tests

### EavContentResolver tests

Cover:

```text
- single item mode
- array mode
- keyed mode
- json casting
- integer / float / boolean / number casting
- is_collection behavior
- sort property behavior
- empty collection behavior
```

Use real Eloquent models with in-memory relations where possible, or simple model instances with `setRelation()`.
Avoid hitting production DB unless needed.

### BlockAttachMap tests

Cover:

```text
- descr_data attaches to content
- slide/list/works attach to sections
- unknown block returns null attach
- isSingle()
- isKeyed()
- is()
```

These tests should be simple and fast.

### CategoryPayloadAssembler tests

Optional in this task.
Add only if the fixture is already clear enough.
Do not overfit the assembler test to implementation internals.
Prefer endpoint tests for public contract.

## Stage 5 — Add architecture boundary checks

Without adding dependencies, create a small Pest/PHPUnit test that scans relevant source files.

Suggested checks:

```text
Resources must not contain:
  - DB::
  - ::where(
  - query()
  - firstOrFail(
  - new BlockCategoryRepository

Controllers should not contain direct model querying except explicitly allowed transitional cases.
```

Do not make this test too naive if it causes false positives.
Whitelist known transitional files only with explicit comments.

Initial expected result:

```text
BlockCategoryController::offers() may be a known transitional violation.
```

The test may either:

```text
A. fail and force cleanup in this task, or
B. mark offers() as an explicit temporary allowlist with a TODO and audit reference.
```

Prefer A if the cleanup is small and safe.

## Stage 6 — Optional small boundary cleanup

After tests exist and pass, evaluate `BlockCategoryController::offers()`.

Current suspected behavior:

```text
- controller fetches BlocksCategories by key
- controller fetches Block by key = offers
- controller queries items and propertyValues directly
- controller returns ad-hoc JSON
```

Preferred direction:

```text
BlockCategoryController::offers()
  → BlockCategoryRepository::getOffersForCategory($locale, $slug)
    → prepared category/block/items data
      → existing BlockItemResource or small response builder
```

Constraints:

```text
- preserve current response keys: category, block, items
- preserve route URL
- preserve status codes
- do not introduce a large service layer
- do not change frontend
```

If cleanup is done, add/extend a feature test for:

```text
GET /{prefix}/en/blocks/categories/offers/{slug}
```

If cleanup is not done, document why and recommend a separate task.

## Stage 7 — Code style and validation

Run as available:

```text
php artisan test
php artisan test --filter=BlockCategoryEndpointTest
php artisan test --filter=EavContentResolverTest
php artisan test --filter=BlockAttachMapTest
./vendor/bin/pint --test
```

If Pint reports changes but the task scope is not style cleanup, either:

```text
- run Pint only on touched files, or
- report existing style drift without broad formatting
```

Do not run global formatting across unrelated files unless explicitly approved.

## Optional composer scripts

If useful and low-risk, add scripts using already installed tools:

```json
{
  "scripts": {
    "test": [
      "@php artisan config:clear --ansi",
      "@php artisan test"
    ],
    "test:backend-contract": [
      "@php artisan test --filter=BlockCategoryEndpointTest"
    ],
    "lint:php": [
      "./vendor/bin/pint --test"
    ]
  }
}
```

Do not remove existing scripts.
Do not add unavailable binaries.

## Forbidden changes

Do not:

```text
- change database schema
- rename public API keys
- change frontend files
- rewrite CategoryPayloadAssembler
- rewrite EavContentResolver broadly
- rewrite all controllers
- install Larastan/PHPStan without approval
- change seed content
- depend on production database dumps for tests
- assert full large HTML strings in tests
- hide a route-prefix problem by changing tests only
```

## Expected deliverables

Final agent report must include:

```text
1. Files inspected.
2. Tests added.
3. Fixture strategy used.
4. Actual route prefix confirmed.
5. Commands run and results.
6. Contract fields protected.
7. Architecture boundary checks added.
8. Whether offers() was refactored or left as known debt.
9. Remaining risks.
10. Recommended TASK-BE-006.
```

## Success criteria

This task is successful if:

```text
- backend has real executable tests beyond skeleton defaults
- critical category endpoint contract is protected
- EAV resolver behavior is protected
- BlockAttachMap behavior is protected
- future agents can run tests before/after refactors
- route prefix behavior is no longer ambiguous
- at least one layer-boundary guard exists
```

## Failure criteria

This task fails if:

```text
- tests require production database state
- tests are brittle full-payload snapshots of long HTML
- tests pass while not exercising the real endpoint
- route prefix is guessed instead of verified
- broad unrelated formatting is applied
- public response shape changes
- frontend compatibility is broken
```

## Recommended follow-up candidates

Depending on results, propose one of:

```text
TASK-BE-006 — Route Registration Cleanup and API Prefix Normalization
TASK-BE-006 — Offers Endpoint Repository Boundary Cleanup
TASK-BE-006 — Larastan/PHPStan Introduction with Baseline
TASK-BE-006 — EAV Query Performance and Index Audit
TASK-BE-006 — Filament Validation Hardening for Block Content Editing
```

## Core reminder

The goal is not to make the backend perfect in one pass.

The goal is to make future backend work verifiable.

The correct result is:

```text
working tests
known contracts
known route behavior
less uncertainty
safer next refactor
```
