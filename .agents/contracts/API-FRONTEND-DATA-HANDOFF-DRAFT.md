# DRAFT CONTRACT — API / Frontend Data Handoff

## Status

Draft / prototype for the next frontend-contract pass.

This document intentionally does not finalize all frontend requirements. It captures current backend payload facts and likely frontend expectations.

---

## 1. Standard category endpoint

```text
GET /api/{locale}/blocks/categories/{slug}
```

Envelope:

```text
response.data.data
```

Top-level expected fields:

```text
id
key
name
description
content
parent_id
created_at
updated_at
section
sections
subcategories
blocks
children
```

Frontend should treat:

```text
key/slug as business identifier
id as render/internal identifier only
content as page/category content object
subcategories as service tree/category cards
blocks as block-driven content zones
```

---

## 2. Service category / service page content

Likely frontend needs:

```yaml
category:
  key: string
  name: string
  content:
    title: string
    descr: string
    content: html|string
    metadata: object|null
    priority: number|string|null
  subcategories:
    - slug/key
    - title/name
    - descr
    - content
    - childs
  blocks:
    - block_key
    - items/properties
```

Service offers are expected as package-like items generated from `ServicesBlockSeeder`.

Potential frontend package fields:

```text
name/title
price
term/timeline
features
featured
icon
url
descr/desc
content
```

---

## 3. Individual CP endpoint

```text
GET /api/{locale}/blocks/categories/offers/{proposalKey}
```

Envelope:

```text
flat JSON, not response.data.data
```

Shape:

```json
{
  "category": {},
  "block": {},
  "items": []
}
```

CP payload properties expected after EAV flattening:

```text
title
content
acticle
hero
benefits
extras
important
items
includes
reelsSystem
```

UI sections:

```text
hero -> first screen
benefits -> value cards
extras -> business outcomes
important -> feature/functionality groups
items -> price packages
includes -> all packages include
acticle -> final persuasive/closing content
```

---

## 4. Locale behavior

```text
Backend route locale filters EAV values by locale.
If locale properties are empty and seeder skipped them, endpoint may omit localized values rather than fallback automatically.
```

Current practical content policy:

```text
ru: production source
EN: currently populated for service offers, service category descriptions, and Vietnam CP batch
VI: placeholder unless explicitly filled
```

Frontend should not assume VI content exists just because `vi.properties` appears in source JSON.

---

## 5. Known asymmetries

```text
standard category endpoints -> Laravel Resource envelope `.data`
offers endpoint -> flat response
legacy key `childs` exists
legacy typo `acticle` exists
`section` may represent locale/scope
```

Do not normalize these in frontend or backend without a coordinated migration.

---

## 6. Future calculator / order payload placeholder

Future product direction:

```text
Offers / packages
  -> calculator items and prices
  -> simple order payload
  -> dynamic commercial proposal preview
```

Likely future fields:

```yaml
calculator_package:
  key: string
  title: string
  base_price: number|string
  currency: string
  features: array
  options: array

calculator_option:
  key: string
  title: string
  price: number|string
  unit: string
  is_default: boolean
  is_required: boolean

quote_request:
  package_key: string
  selected_options: array
  total: number|string
  contact: object
  comment: string
```

This is not implemented as a final contract yet.
