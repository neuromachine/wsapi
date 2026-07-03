# CONTENT-FILE-FORMATS — Current WS Seed Content Formats

## Purpose

This file defines the accepted JSON source formats for current content seeding work.

Do not redesign all formats now. Normalize only what is needed for the next practical content tasks.

---

## Format A — category description file

Path examples:

```text
storage/app/blocks/cat/services.json
storage/app/blocks/cat/prodvizenie.json
storage/app/blocks/cat/portfolio/barma.json
```

Accepted localized format:

```json
{
  "ru": {
    "descr": "...",
    "content": "..."
  },
  "en": {
    "descr": "...",
    "content": "..."
  },
  "vi": {
    "descr": "...",
    "content": "..."
  }
}
```

Accepted legacy non-localized format:

```json
{
  "descr": "...",
  "content": "..."
}
```

Preferred for new work: localized format.

---

## Format B — descr_data item file

Path:

```text
storage/app/blocks/blocks/items/descr_data/{categoryKey}.json
```

Shape:

```json
{
  "key": "services",
  "name": "Услуги",
  "block": "descr_data",
  "properties": {
    "title": "...",
    "descr": "...",
    "content": "...",
    "metadata": {},
    "priority": 2
  },
  "en": {
    "properties": {
      "title": "...",
      "descr": "...",
      "content": "...",
      "metadata": {},
      "priority": 2
    }
  },
  "vi": {
    "properties": {}
  }
}
```

Root `properties` means `ru`.

---

## Format C — service package group file

Current legacy path:

```text
storage/app/blocks/items/{categoryKey}.json
```

Current shape:

```json
{
  "items": [
    {
      "name": "Базовый",
      "key": "landing_basic",
      "properties": {
        "title": "...",
        "descr": "...",
        "price": "...",
        "timeline": [],
        "features": [],
        "icon": "..."
      }
    }
  ]
}
```

Recommended localized extension for new work:

```json
{
  "items": [
    {
      "name": "Базовый",
      "key": "landing_basic",
      "properties": {
        "title": "...",
        "descr": "..."
      },
      "en": {
        "name": "Basic",
        "properties": {
          "title": "...",
          "descr": "..."
        }
      },
      "vi": {
        "name": "...",
        "properties": {}
      }
    }
  ]
}
```

Root `properties` means `ru`.

`ServicesBlockSeeder` must be updated to read `en.properties` and `vi.properties` if present.

---

## Format D — individual commercial offer file

Path:

```text
storage/app/blocks/blocks/items/ind_offers/{key}.json
```

Current block:

```text
ind_offers
```

Current properties:

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

Recommended shape:

```json
{
  "key": "visarun_system",
  "name": "Visa Run Booking System",
  "block": "ind_offers",
  "properties": {
    "title": "...",
    "content": "...",
    "acticle": "...",
    "hero": {},
    "benefits": {},
    "includes": [],
    "items": {},
    "extras": {},
    "important": {}
  },
  "en": {
    "name": "Visa Run Booking System",
    "properties": {
      "title": "...",
      "content": "...",
      "acticle": "...",
      "hero": {},
      "benefits": {},
      "includes": [],
      "items": {},
      "extras": {},
      "important": {}
    }
  },
  "vi": {
    "name": "...",
    "properties": {}
  }
}
```

Root `properties` means `ru`.

---

## Format E — navigation item file

Path:

```text
storage/app/blocks/blocks/items/navigation/services-en.json
```

Shape:

```json
{
  "name": "Services",
  "description": "",
  "scope": "en",
  "properties": {
    "anchor": "Services",
    "link": "/services",
    "sort": "1"
  }
}
```

Navigation uses one file per scope. Do not mix this with service offer package format.

---

## General rules for agents

1. Preserve existing keys even if naming is imperfect: `acticle`, `childs`, `items`.
2. Do not rename content files casually.
3. Do not change API Resource output in seeding tasks.
4. Prefer extending JSON shape over changing current root `properties` behavior.
5. Root `properties` means `ru` unless a seeder says otherwise.
6. New `en` and `vi` sections should use nested `{ "properties": ... }`.
7. For JSON-valued fields, keep arrays/objects as JSON values, not strings.

