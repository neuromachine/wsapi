# REPORT-SEED-001 — Service Offers Locale Expansion

## 1. Overview
The objective was to update the seeding logic for service offer packages (`offers` block) to natively support multiple locales without modifying API contracts or frontend consumption code. This was accomplished by expanding the `ServicesBlockSeeder.php` iteration logic and creating correctly localized source JSON structures for priority packages.

## 2. Files Modified
- **`database/seeders/ServicesBlockSeeder.php`**
  - Upgraded the inner loop to iterate through `['ru', 'en', 'vi']`.
  - Empty property objects (`{}`) are explicitly bypassed, resolving the constraint regarding empty/broken fallback data.
- **`storage/app/blocks/items/posadocnaia-stranica.json`**
  - Added full English translations inside the `"en": { "properties": { ... } }` object for all four landing page packages.
  - Added empty `"vi": { "properties": {} }` structure for safe validation.
- **`storage/app/blocks/items/internet-katalog.json`**
  - Added full English translations for all four online catalog packages.
  - Added empty `"vi": { "properties": {} }` structure.

## 3. How `ServicesBlockSeeder` Maps Locales
The seeder dynamically determines the properties mapping for each targeted localization boundary:
- For `ru`, it strictly references the root `properties` (legacy schema constraint met).
- For `en` and `vi`, it references `$itemDef[$locale]['properties']`.
- If the properties payload is missing or empty, it continues without saving hollow entities, fully adhering to the approved "don't create empty EAV attributes" rule.

## 4. Manual Verification Notes
To verify this flow on the local environment, you can safely run:

```bash
php artisan db:seed --class=ServicesBlockSeeder
```

Following the seeder's successful output, request the following localized endpoints to observe the data projection:

```bash
GET /api/ru/blocks/categories/posadocnaia-stranica
GET /api/en/blocks/categories/posadocnaia-stranica
GET /api/vi/blocks/categories/posadocnaia-stranica
```

Note: The `vi` endpoint will silently omit the `offers` array fields due to the correctly ignored hollow `{}` properties.
