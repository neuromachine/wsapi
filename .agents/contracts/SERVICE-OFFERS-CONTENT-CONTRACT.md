# Contract — Service Offer Packages (`offers` block)

## Scope

This contract is for offer/package data displayed on service category pages.

Content source:

```text
storage/app/blocks/items/{service-category-key}.json
```

Seeder:

```text
php artisan db:seed --class=ServicesBlockSeeder
```

Endpoint example:

```text
GET /api/en/blocks/categories/posadocnaia-stranica
```

---

## Difference from `ind_offers`

Service offers are category-attached packages, not standalone commercial proposal pages.

They are usually smaller and focused on one service category:

```text
Landing page packages
Corporate site packages
Online catalog packages
Marketing platform packages
etc.
```

Individual CP files are broader, more narrative, and live under `ind_offers`.

---

## Locale shape

Current shape:

```json
{
  "key": "posadocnaia-stranica",
  "name": "...",
  "block": "offers",
  "properties": { ... },
  "en": { "name": "Landing Page", "properties": { ... } },
  "vi": { "properties": {} }
}
```

`ServicesBlockSeeder` rules:

```text
ru -> root properties
en/vi -> {locale}.properties
empty properties -> skip silently
```

---

## Normal content expectation

Use this type for structured offer packages tied to service pages:

```text
items / packages
price
term
features
short descriptions
```

Do not turn service offers into a full commercial proposal narrative unless the page UI needs it.

