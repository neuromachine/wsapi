# AUDIT-BE-004 — Backend Architecture Audit

## 1. Executive Summary
This audit evaluated the current architectural state of the WS CODE backend following the initial refactoring efforts in TASK-BE-002 (Read-Side) and TASK-BE-003 (Seed Pipeline). 
The overall architecture shows strong progress toward a clean `Controller -> Repository -> Assembler -> Resource` flow, but several significant risks remain. The most critical finding is the absolute lack of automated contract tests, meaning any further structural changes carry a high risk of breaking frontend expectations. Additionally, Laravel 11 route registration is duplicated, and some boundary violations (fat controllers, `attributesToArray()` leakage) still exist.

## 2. Repository Snapshot
- **Framework:** Laravel 11 (inferred from `bootstrap/app.php` routing setup).
- **Core Subsystems:** Blocks/EAV data model, forms, seeders.
- **Environment:** Local CLI execution (PHP/Composer unavailable in current test run, requiring static inspection).
- **Test Coverage:** Non-existent (only `ExampleTest.php` found in Pest/PHPUnit directories).

## 3. What TASK-BE-002 Changed / Current Read-Side State
TASK-BE-002 successfully introduced `CategoryPayloadAssembler`, which orchestrates the EAV mapping (via `EavContentResolver`) and policy enforcement (via `BlockAttachMap`). `BlockCategoryRepository` correctly handles eager-loading closures. However, the final serialization step in `BlockCategoryResource` still relies on `attributesToArray()`, leaving the API contract vulnerable to database schema changes.

## 4. What TASK-BE-003 Changed / Current Seed Pipeline State
TASK-BE-003 introduced `ImportHelper` to standardize database upserts. This effectively eliminated duplication across seeders while maintaining compatibility. The file-based JSON pipeline (`BlockContentHelper`) remains intact. This layer is currently stable.

## 5. Findings by Severity

### P0 — Critical / Must Fix Before Further Work
- **Zero API Contract Tests:** The `tests/Feature` and `tests/Unit` directories contain only dummy example tests. Without contract tests for endpoints like `GET /en/blocks/categories/services`, further refactoring (like fixing `attributesToArray()`) is unsafe.

### P1 — High / Next Refactor Candidates
- **Double Route Registration:** `routes/api.php` is registered both in Laravel 11's new `bootstrap/app.php` and manually in `AppServiceProvider::boot()`. This can cause duplicate route loading or double-prefixing.
- **Resource Leakage:** `BlockCategoryResource` uses `array_merge($this->attributesToArray(), [...])`. This exposes internal database columns to the frontend payload, risking contract breaks if the schema changes.

### P2 — Medium / Important Architecture Debt
- **Fat Controller Boundary Violation:** `BlockCategoryController::offers()` directly queries Eloquent (`BlocksCategories::where...`, `Block::where...`) and manually constructs a JSON response array, bypassing the Repository and Resource layers.
- **EAV Model Inconsistency:** The `BlockItem` model contains two identical relationships (`properties()` and `propertyValues()`), causing confusion regarding which one should be eager-loaded.

### P3 — Low / Cleanup / Documentation
- **BlockAttachMap Hardcoding:** `BlockAttachMap` contains a `// TODO: make system attach` note to move mappings to the database. Currently, it hardcodes logic like mapping `slide` to `sections`.
- **Forms Controller:** `FormController::store` creates models directly. This is acceptable for simple operations but could be abstracted if business logic grows.

## 6. Layer-by-Layer Audit

### Routing / Bootstrap
- **Issue:** Duplicate registration of API routes. `bootstrap/app.php` correctly registers `api: __DIR__.'/../routes/api.php'`, but `AppServiceProvider` also manually registers it with an `api` prefix.
- **Locale:** `SetLocale` middleware applies the `{locale}` prefix appropriately.

### Controllers
- Controllers generally delegate properly, EXCEPT `BlockCategoryController::offers()`, which manually performs queries and builds JSON arrays.

### Repositories
- `BlockCategoryRepository` is doing heavy lifting with nested closures to load EAV data scoped to the current locale. It's correct but risks becoming a God object if filtering requirements grow.

### Resources
- `BlockResource` uses explicit mapping, which is excellent.
- `BlockCategoryResource` relies on `attributesToArray()`, which is a known leakage risk.

### CategoryPayloadAssembler
- Focused, clean, and correctly handles priority sorting.

### EavContentResolver
- Pure transformer. Works well for casting types (`value_type`) and flattening items.

### BlockAttachMap
- Acts as a rigid compatibility policy. Functional but constitutes technical debt until moved to the database schema (`blocks.attach`).

### EAV Models
- `BlockItem` has duplicate methods for the same relation (`properties` vs `propertyValues`).
- `BlocksCategories` has commented out legacy code for `childrenRecursive`.

### Seeders / Import
- Stabilized in TASK-BE-003. Idempotent and uses `ImportHelper`.

### Forms
- Basic but functional. Validates via FormRequest and creates `FormStatus::Pending`.

### Filament / Admin
- `BlockItemPropertyValueResource` edits EAV properties directly. There is a risk that Filament users can bypass the strict constraints of the `BlockItemProperty` definition (e.g., arbitrarily choosing `value_type` for a specific item value rather than inheriting the property's type).

### Tests / CI / Tooling
- `pest` and `pint` are present in dependencies, but no actual tests have been written. 

### Frontend Contract Impact
- The `response.data.data` envelope is respected in standard Resource returns, but `BlockCategoryController::offers()` does NOT wrap its output in `.data`, which might create inconsistencies on the frontend depending on how the `offers` endpoint is consumed.
- EAV legacy keys (like `childs`, `section`) are still present and preserved by `CategoryPayloadAssembler`.

## 7. Contract Risks
- Changing table columns in `blocks_categories` will immediately change the API response due to `attributesToArray()`.
- The `offers` endpoint returns a flat JSON object without a Resource envelope. Modifying this controller might break frontend consumers expecting that exact flat shape.

## 8. Data Quality Risks
- Filament admin users editing `BlockItemPropertyValue` can potentially select conflicting `value_type` settings that do not match the expected property schema, leading to rendering errors in `EavContentResolver`.

## 9. Recommended Next Tasks

TASK-BE-006 — Route and API Bootstrap Cleanup
Priority: P1
Type: audit/cleanup
Depends on: none
Reason: Prevent duplicate route registration in Laravel 11.
Expected output: Removal of API registration from AppServiceProvider.
Frontend impact: none

TASK-BE-007 — Offers Endpoint Boundary Refactor
Priority: P2
Type: refactor
Depends on: TASK-BE-005
Reason: `BlockCategoryController::offers()` bypasses Repository/Resource layers.
Expected output: Logic moved to Repository and new Resource created.
Frontend impact: none (preserve flat shape or handoff if changing to envelope).

TASK-BE-008 — Explicit Resource Serialization Hardening
Priority: P1
Type: refactor
Depends on: TASK-BE-005
Reason: `BlockCategoryResource` uses `attributesToArray()`.
Expected output: Explicit field map implemented.
Frontend impact: none

## 10. Recommended Modification of TASK-BE-005
TASK-BE-005 (Backend Contract Safety Net) is **critical** and must be the immediate next step. 
**Modification:** Make sure the tests explicitly cover the `GET /en/blocks/categories/offers/{slug}` endpoint to lock in its non-standard flat JSON structure before any refactoring of that controller begins. Also, ensure tests assert the presence of legacy EAV keys (e.g., `sections`, `content`).

## 11. Backend → Frontend Handoff Recommendations
- Frontend should verify if the `/offers/` endpoint expects a flat response or if it can handle a standard `.data` envelope. If it can handle `.data`, we should migrate the endpoint to a Resource.

## 12. What Not To Refactor Yet
- Do NOT refactor `BlockCategoryController::offers()` or `BlockCategoryResource` until TASK-BE-005 is completed.
- Do NOT attempt to migrate `BlockAttachMap` logic into the database yet.
- Do NOT consolidate the duplicate `BlockItem` relationships until test coverage guarantees EAV resolution won't break.

## 13. Open Questions for Human Review
- Should the `offers` endpoint eventually conform to the standard Laravel Resource `.data` envelope, or is the frontend tightly coupled to its flat response?
- Does the business logic require Filament forms to strictly enforce `value_type` based on the parent property, or is manual override intentional?

## 14. Commands Run / Commands Not Run
- Run but failed (environment missing PHP/Composer): `php artisan route:list`, `php artisan test`, `./vendor/bin/pest`, `./vendor/bin/pint --test`, `composer validate`.
- Static inspection used instead.

## 15. Final Recommendation
Halt structural changes. The architecture is moving in a good direction, but it is currently operating without a safety net. Proceed immediately with TASK-BE-005 to establish Pest/PHPUnit contract tests for the public API.
