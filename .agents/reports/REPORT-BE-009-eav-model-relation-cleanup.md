# REPORT-BE-009 — EAV Model Relation Cleanup

## 1. Relation Methods Before/After
**Before:**
`BlockItem` had two relationships exposing the exact same EAV values:
- `public function properties()`
- `public function propertyValues()`

**After:**
- `propertyValues()` is designated as the canonical relation representing the underlying `block_item_property_values` table.
- `properties()` has been marked with `@deprecated` and internally modified to `return $this->propertyValues();` to prevent duplicate logic.

## 2. Chosen Canonical Relation
`propertyValues()`

## 3. Compatibility Alias
The `properties()` method remains as a compatibility alias inside `BlockItem` to ensure that any unmigrated codebase corners relying on `$item->properties` or `with('properties')` do not immediately break.

## 4. Usage Sites Updated
- `app/Models/Block.php`: Updated eager loads in `items()` and `itemsCategory()` to use `propertyValues.property` instead of `properties.property`.
- `app/Filament/Resources/BlockItemResource.php`: Removed the redundant (and non-functional) `Repeater::make('properties')` UI block, retaining only the complete `Repeater::make('propertyValues')`.
- API resources (e.g., `app/Http/Resources/BlockItemResource.php`) were verified to already be utilizing `$this->propertyValues` under the hood.

## 5. Public API Compatibility Statement
The public JSON structure remains perfectly backward compatible. The output key `"properties"` in the public API continues to be used, even though internally the data is sourced from the canonical `propertyValues` relation.

## 6. Commands Run or Not Run
Static analysis and code modifications were performed successfully. Tests (`pest`) were **not run dynamically** due to environmental constraints (lack of `php` executable), consistent with prior tasks.

## 7. Recommended Next Task
Proceed to `TASK-BE-010 — EAV Resolver / BlockItemResource Consistency`.
