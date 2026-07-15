# OFFERS-SEEDING-CONTRACT — Service Offers and CP Packages

## Purpose

This contract defines how to seed package/offers content before building the frontend calculator and dynamic commercial proposal flow.

## Two different concepts

### 1. `offers`

Block key:

```text
offers
```

Meaning:

```text
service package cards attached to service categories
```

Example future use:

```text
Landing page package: Basic / Pro / Premium
Catalog package: Basic / Advanced / Automation
```

Current endpoint impact:

```text
GET /api/{locale}/blocks/categories/{serviceCategorySlug}
```

The category resource loads category blocks and child category data.

### 2. `ind_offers`

Block key:

```text
ind_offers
```

Meaning:

```text
individual commercial proposals / industry-specific offers / long-form proposal content
```

Current endpoint impact:

```text
GET /api/{locale}/blocks/categories/offers/{slug}
```

The offers endpoint returns flat JSON:

```json
{
  "category": {},
  "block": {},
  "items": []
}
```

Do not change this shape in seeding tasks.

---

## Service offer package fields

For block `offers`, keep or extend the existing properties:

```text
title
url
price
descr
content
image
files
timeline
features
icon
```

Recommended package item:

```json
{
  "name": "Basic",
  "key": "landing_basic",
  "properties": {
    "title": "Базовый пакет",
    "descr": "...",
    "price": {
      "value": 40000,
      "currency": "RUB",
      "label": "от 40 000 ₽"
    },
    "timeline": ["5–7 дней"],
    "features": ["..."],
    "icon": "window"
  },
  "en": {
    "name": "Basic",
    "properties": {
      "title": "Basic package",
      "descr": "...",
      "price": {
        "value": 40000,
        "currency": "RUB",
        "label": "from 40,000 RUB"
      }
    }
  }
}
```

## Individual offer fields

For block `ind_offers`, current fields:

```text
title
content
acticle
hero
benefits
includes
items
reelsSystem
extras
important
```

Do not rename `acticle` yet.

## Calculator readiness

The future calculator can be built from these fields:

```text
package key
package title/descr
price object
features/includes
optional items[]
order/quote payload
```

Do not build the calculator in these backend seeding tasks.

## Locales

Required minimum for next work:

```text
ru: real source content
 en: at least structure + content for priority packages
 vi: structure can exist as empty properties or deferred content
```

Preferred:

```text
root properties = ru
en.properties = English
vi.properties = Vietnamese
```

## Frontend contract

For standard category endpoints, frontend expects Laravel Resource envelope:

```text
response.data.data
```

For `offers/{slug}`, the endpoint currently preserves flat JSON:

```text
category
block
items
```

Do not alter endpoint response shape in seeding tasks.

