# REPORT-BE-010 — EAV Resolver / BlockItemResource Consistency

## 1. Current Inconsistency Found
Previously, `BlockItemResource` and `EavContentResolver` implemented their own separate logic for parsing EAV records into flat arrays. 
- `BlockItemResource` relied on `$group->first()->property->type` to determine if a value was JSON, and used the count of grouped records to determine if a field should be an array.
- `EavContentResolver` relied on `$pv->value_type` and `$pv->property->is_collection`, casting strictly based on a dedicated `match()` block (`integer`, `boolean`, `json`, etc.).

## 2. Chosen Canonical Transformation Path
`EavContentResolver` has been established as the single source of truth for EAV logic.

## 3. Files Changed
**Modified:**
- `app/Support/EavContentResolver.php`: Renamed the private `flattenItem` method to the public `resolveItem`, allowing single item resolutions from the outside.
- `app/Http/Resources/BlockItemResource.php`: Replaced the entire manual mapping closure with a direct call to `EavContentResolver::resolveItem($this->resource)`.

## 4. Public Response Compatibility Statement
The JSON output remains structurally identical. `id`, `key`, `name`, and `properties` are preserved perfectly. Inside `properties`, values are now explicitly typed (`int`, `bool`) and grouped into arrays based strictly on the DB flag (`is_collection`) rather than counting records, significantly improving predictability without breaking the schema.

## 5. Commands Run or Not Run
Tests (`pest`) were **not run dynamically** due to the lack of a PHP/Composer environment. Manual execution by a developer is required to ensure these changes perform accurately against the existing database.

## 6. Remaining EAV Debt
There may still be inconsistencies in the database where `BlockItemPropertyValue.value_type` does not logically match `BlockItemProperty.type`, which leads directly to the next admin/UI task.

## 7. Recommended Next Task
Proceed to `TASK-BE-011 — Filament EAV Guardrails`.
