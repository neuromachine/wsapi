# TASK-BE-009 — EAV Model Relation Cleanup

## Status

Follow-up practical backend refactor after BE-008.

## Type

Model relation cleanup / EAV naming consistency.

## Priority

P2.

## Problem

`BlockItem` currently exposes overlapping or duplicate relationships for EAV values, commonly referred to as:

```text
properties
propertyValues
```

This creates confusion in repository eager loading and resource transformation.

The goal is to reduce ambiguity while preserving backward compatibility for existing code paths.

## Main Goal

Establish one canonical relationship name for EAV values on `BlockItem`, while keeping a temporary compatibility alias if needed.

## Primary Target

```text
app/Models/BlockItem.php
```

Inspect usage across:

```text
app/Repositories/*.php
app/Http/Resources/*.php
app/Support/*.php
app/Filament/Resources/**/*.php
```

## Desired Direction

Preferred canonical relation:

```php
public function propertyValues()
```

Reason:

```text
It describes the actual table/model: block_item_property_values.
```

If `properties()` is currently used in important code, do not remove it immediately. Convert it to a compatibility alias:

```php
/**
 * @deprecated Use propertyValues() for EAV values.
 */
public function properties()
{
    return $this->propertyValues();
}
```

Only remove the alias if a full usage scan proves it is safe.

## Allowed Changes

Allowed:

```text
- update BlockItem relation methods
- update repository eager-loading names if safe
- update resources/support classes to canonical propertyValues where appropriate
- add compatibility comments
- update report
```

## Forbidden Changes

Do not:

```text
- change database schema
- rename tables or columns
- change public API keys
- refactor EavContentResolver behavior broadly
- change frontend
- change Filament behavior unless it only references the canonical relation safely
```

## Manual Verification

After the change, manually inspect or run:

```text
GET /api/en/blocks/categories/services
GET /api/en/blocks/categories/offers/{slug}
```

Expected:

```text
- no missing relation errors
- EAV values still appear in content/subcategories/items
- offers endpoint remains flat JSON
```

Run if available:

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
```

## Expected Report

Create:

```text
.agents/reports/REPORT-BE-009-eav-model-relation-cleanup.md
```

Include:

```text
1. Relation methods before/after.
2. Chosen canonical relation.
3. Whether compatibility alias remains.
4. Usage sites updated.
5. Public API compatibility statement.
6. Commands run or not run.
7. Recommended next task.
```

## Success Criteria

```text
- EAV relation naming becomes clearer.
- Existing API responses remain compatible.
- Compatibility alias prevents unnecessary breakage.
- Future repository/resource code has one preferred relation name.
```

