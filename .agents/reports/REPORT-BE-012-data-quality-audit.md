# REPORT-BE-012 — Data Quality Audit

## 1. Scope of Audit
A static analysis of the EAV seed files (`database/seeders` and `storage/app/blocks`) was conducted to identify inconsistencies across encoding, locales, and JSON types.

## 2. Encoding Artifacts
No immediate encoding anomalies (e.g., `ЧТ`, mojibake) were found in the static JSON payload files located in `storage/app/blocks`. The string payloads appear to be strictly valid UTF-8 and unicode-escaped strings (`\uXXXX`).

## 3. Missing Locales
**Critical Issue Found:** 
All major content seeders (`ServicesBlockSeeder`, `KpBlockSeeder`, `BlockItemPropertyValuesSeeder`) strictly hardcode the `ru` locale when inserting EAV values.
```php
// From ServicesBlockSeeder.php
ImportHelper::upsertPropertyValue($itemId, $propertyId, 'ru', $propValue);
```
**Impact:** `en` and `vi` locales are entirely absent from the seeded EAV property values. If the API queries for `GET /api/en/blocks/categories/services`, it will currently return empty values for all properties scoped by locale. 

## 4. JSON Validation and Type Safety
**Seeder Implementation:** The `ImportHelper` and seeders correctly infer the `json` type by checking `is_array($propValue) ? 'json' : 'string'`. This prevents invalid strings from being marked as JSON.
**Admin UI Risk:** Prior to `TASK-BE-011`, admins could manually break this by selecting `json` but entering a plain string. `TASK-BE-011` added UI guardrails to mitigate this risk.

## 5. Duplicate Keys / EAV Integrity
Since the `ImportHelper` utilizes `updateOrInsert` strictly mapped by `item_id` and `property_id`, duplicate properties within the same `locale` are structurally impossible at the database level.

## 6. Recommendations
1. **Locale Expansion:** Modify `BlockContentHelper` to load structured locales (e.g., parsing `.en.json`, `.vi.json`) or implement a fallback mechanism in the Repository so that `ru` values are returned when `en` is empty.
2. **Database Clean-Up Command:** In the future, create an Artisan command (e.g., `php artisan ws:audit-eav`) that programmatically scans the live `block_item_property_values` table for type-mismatches and automatically corrects them, as relying entirely on the initial seeder state is unsafe for a dynamic system.

---
**Sequence Complete:** This concludes the `BE-008 → BE-012` backend refactoring sequence.
