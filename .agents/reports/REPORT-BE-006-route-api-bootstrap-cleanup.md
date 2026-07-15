# REPORT-BE-006 — Route and API Bootstrap Cleanup

## 1. Goal
Remove redundant API route registration to ensure API endpoints are not duplicated or double-prefixed by the framework, eliminating ambiguity during testing and frontend consumption.

## 2. Investigation
The audit in `TASK-BE-004` correctly identified that:
- `bootstrap/app.php` natively registers API routes (`api: __DIR__.'/../routes/api.php'`).
- `app/Providers/AppServiceProvider.php` was manually reloading these routes with the same `api` prefix and middleware.

## 3. Changes Made
**Modified:**
- `app/Providers/AppServiceProvider.php`: Removed the explicit `Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));` call from the `boot` method.

## 4. Expected Impact
This ensures Laravel 11's standard request lifecycle takes precedence and routes are registered only once. Because `bootstrap/app.php` correctly prefixes API routes, the public API contract URLs remain perfectly identical (e.g., `/api/en/blocks/categories/services`), but without a duplicate shadow route table holding an identical mapping.

## 5. Required Validation Note
**Validation Constraints:** As established during `TASK-BE-004` and `TASK-BE-005`, the current agent environment cannot execute PHP or Composer commands (`php artisan route:list`, `./vendor/bin/pest`).
- *Tests and route listings were not run dynamically.*
- A human developer is required to run `./vendor/bin/pest` and `php artisan route:list` to fully confirm this cleanup functions as intended before deploying or merging.
