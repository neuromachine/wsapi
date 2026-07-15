# BE-10 — Content Source Registry

## Purpose

Registry of JSON source paths, meaning, responsible seeders, and expected output families.

---

## 1. Path overview

| Source path | Meaning | Seeder / reader | Output family |
|---|---|---|---|
| `storage/app/blocks/categories.json` | Root category registry | `BlocksMainCategoriesSeeder` | `blocks_categories` roots |
| `storage/app/blocks/tree.json` | Service category tree | `BlocksServicesCategoriesSeeder` | service category hierarchy |
| `storage/app/blocks/cat/*.json` | Category descriptors | `BlocksCategoriesSeeder`, category helper methods | category metadata / descriptions |
| `storage/app/blocks/cat/{group}/*.json` | Nested category files | `BlockContentHelper`-based category seeders | child category / grouped category content |
| `storage/app/blocks/items/{categoryKey}.json` | Service offer package data for a category | `ServicesBlockSeeder` | `offers` block items for service pages |
| `storage/app/blocks/blocks/items/ind_offers/*.json` | Individual commercial proposals | `BlockForCpDataSeeder` | `ind_offers` block items and offer endpoint |
| `storage/app/blocks/blocks/items/descr_data/*.json` | Descriptive EAV item per category/page | `BlockItemsForCategoriesDesrDataSeeder` | `data.content`, category SEO/content payload |
| `storage/app/blocks/blocks/items/pages/*.json` | Static pages | `BlockForPagesDataSeeder` | page item payloads |
| `storage/app/blocks/cat/portfolio/*.json` | Portfolio category/project descriptors | `BlockForPortfolioDataSeeder` | portfolio items/categories |
| `storage/app/blocks/blocks/items/navigation/*.json` | Navigation entries by scope | `BlocksForNavigationSeeder` | navigation payload |
| `storage/app/blocks/cat/main/*.json` | Main page sections | `BlocksForMainSectionsSeeder` | main page section payload |

---

## 2. Service offers

### Source

```text
storage/app/blocks/items/{categoryKey}.json
```

### Seeder

```text
ServicesBlockSeeder
```

### Locale format

```text
ru -> root properties
en -> en.properties
vi -> vi.properties placeholder allowed
```

### Post-CONTENT status

```text
61 files scanned
61 files translated to English
61 files normalized
0 categories missing EN offers
```

### Recommended seed command

```bash
php artisan db:seed --class=ServicesBlockSeeder
```

### Verify

```text
GET /api/en/blocks/categories/{categoryKey}
```

---

## 3. Individual commercial proposals / `ind_offers`

### Source

```text
storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json
```

### Seeder

```text
BlockForCpDataSeeder
```

### Endpoint

```text
GET /api/{locale}/blocks/categories/offers/{proposalKey}
```

### Locale format

```text
ru -> root properties
en -> en.properties
vi -> vi.properties placeholder allowed
```

### Canonical properties

```text
title
content
acticle
items
hero
benefits
includes
reelsSystem
extras
important
```

### Generated Vietnam tourism CP keys

```text
visa_run_agency
tour_desk_nha_trang
transfer_service_vietnam
hotel_rental_vietnam
beauty_services_tourists
medical_tourism_clinic
```

### Recommended seed command

```bash
php artisan db:seed --class=BlockForCpDataSeeder
```

---

## 4. Service category descriptions

### Source

```text
storage/app/blocks/cat/*.json
```

### Post-CONTENT status

```text
84 files scanned
83 files updated
1 file skipped: _blank.json
```

### EN fields

```text
en.properties.descr
en.properties.content
```

### Caveat

Category description seeding may require seeders that are less safe than content-only seeders. Review idempotency before running broad category seed operations.

---

## 5. Navigation

### Source

```text
storage/app/blocks/blocks/items/navigation/*.json
storage/app/blocks/blocks/navigation.json
```

### Seeder

```text
BlocksForNavigationSeeder
```

### Notes

Navigation item files may use explicit `scope` per file rather than the root/en/vi property model used by content package files.

---

## 6. Pages / Portfolio / Main sections

These families are currently documented but not the main focus of the latest content-production cycle.

Future architecture passes should create specialized contracts for:

```text
Pages content contract
Portfolio content contract
Main page sections contract
Navigation content contract
```
