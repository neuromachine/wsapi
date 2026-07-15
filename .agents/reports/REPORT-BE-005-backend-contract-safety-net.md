# REPORT-BE-005 — Backend Contract Safety Net

## 1. Files Inspected
- `.agents/reports/AUDIT-BE-004-backend-architecture-audit.md`
- `tests/TestCase.php`, `tests/Pest.php`
- `phpunit.xml`
- `app/Http/Controllers/Api/BlockCategoryController.php`
- `app/Http/Resources/BlockCategoryResource.php`
- `app/Http/Resources/BlockResource.php`
- `app/Support/EavContentResolver.php`
- `app/Support/BlockAttachMap.php`
- `routes/api.php`

## 2. Files Created/Changed
**Modified:**
- `tests/Pest.php`: Uncommented `RefreshDatabase` to allow isolated database testing in Pest.

**Created:**
- `tests/Fixtures/EavFixture.php`: A helper builder to isolate test data from seeders.
- `tests/Feature/BlockCategoryServicesContractTest.php`: Tests for `GET /en/blocks/categories/services`.
- `tests/Feature/BlockCategoryOffersContractTest.php`: Tests for `GET /en/blocks/categories/offers/{slug}`.
- `tests/Unit/EavContentResolverTest.php`: Unit tests for resolution permutations (single, keyed, array, types).
- `tests/Unit/BlockAttachMapTest.php`: Unit tests checking existing compatibility map logic.

## 3. Test Strategy Used
**Isolated fixtures (Option A)** were implemented. The `EavFixture` helper class provides a clean way to spin up the required nested structures (categories, blocks, items, properties, values) inside an in-memory SQLite database, guaranteeing that the tests are not subject to seed data drift. 

*Note: Since the runtime environment lacks PHP/Composer, this implementation effectively acts as a **static-only fallback (Option C)** until a developer runs the tests in a properly configured environment.*

## 4. Contract Endpoints Covered
- `GET /en/blocks/categories/services`
- `GET /en/blocks/categories/offers/{slug}`

## 5. Exact Assertions Covered
### Services Endpoint:
- Asserts HTTP 200.
- Asserts presence of `data` root wrapper.
- Asserts structure contains standard keys (`id`, `key`, `name`, `parent_id`, `created_at`, `updated_at`, `blocks`).
- Asserts legacy/compatibility keys are present (`content`, `sections`, `subcategories`, `children`, `childs` inside subcategories).
- Asserts EAV data is flattened correctly in `data.content` and `data.sections`.
- Asserts `section` returns the locale.

### Offers Endpoint:
- Asserts HTTP 200.
- Asserts `data` wrapper is missing (flat JSON response).
- Asserts structure has exactly `category`, `block`, and `items`.
- Asserts `items` list exposes raw properties array instead of flattened EAV content.

### EavContentResolver:
- Asserts `single`, `keyed`, and `array` return modes.
- Asserts boolean logic (`filter_var` usage) and json decoding correctness.
- Asserts `is_collection` correctly aggregates array responses.

### BlockAttachMap:
- Asserts mapping definitions for `descr_data`, `slide`, `list`, `simplehtml`.
- Asserts `isSingle()` and `isKeyed()` behavior.
- Asserts fallback to null for unknown blocks.

## 6. Commands Run and Results
- `.\vendor\bin\pest` — **Failed (Execution not performed).**

## 7. Commands That Could Not Run and Why
Neither `pest`, `phpunit`, `php artisan route:list`, nor `composer validate` could run because the `php` executable is not available in the agent's current Windows environment path. The tests were generated statically.

## 8. Are BE-006, 007, and 008 safe to run?
**No.** Before proceeding to `TASK-BE-006` (Route Cleanup), `TASK-BE-007` (Offers Refactor), or `TASK-BE-008` (Resource Hardening), a human developer **must run the generated Pest suite locally** to ensure the tests pass and fully lock down the current state. 

## 9. Risks or Manual Follow-up
- **Manual Verification:** The developer must run `php artisan test` or `./vendor/bin/pest`.
- **EAV Fixture Adjustments:** Depending on exact database constraints not visible statically, `EavFixture.php` might need slight adjustments (e.g., adding a missing required foreign key field) to run smoothly.
- **SQLite Support:** The `phpunit.xml` targets an in-memory SQLite database. If the actual migrations contain MySQL-specific statements (like `enum`), they might fail during test setup. If this occurs, the DB driver should be adjusted or MySQL-specific migrations bypassed.
