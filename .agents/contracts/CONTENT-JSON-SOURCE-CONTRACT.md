# CONTRACT — Content JSON Source Contract

## Purpose

Rules for creating/editing JSON files that feed the content seed pipeline.

---

## 1. Universal localized item format

Preferred modern format:

```json
{
  "key": "item_key",
  "name": "Russian name",
  "properties": {
    "title": "Russian title"
  },
  "en": {
    "name": "English name",
    "properties": {
      "title": "English title"
    }
  },
  "vi": {
    "properties": {}
  }
}
```

Rules:

```text
- `key` must be stable and URL-safe.
- `properties` at root means RU payload.
- `en.properties` means EN payload.
- `vi.properties` may be empty placeholder.
- Do not write unsupported arbitrary keys unless a seeder/block property exists.
- Preserve legacy typo `acticle` where required.
```

---

## 2. Service offers JSON

Source:

```text
storage/app/blocks/items/{categoryKey}.json
```

Seeder:

```text
ServicesBlockSeeder
```

Expected structure:

```json
{
  "items": [
    {
      "key": "basic-package",
      "name": "...",
      "properties": {
        "title": "...",
        "price": "...",
        "features": []
      },
      "en": {
        "name": "...",
        "properties": {
          "title": "...",
          "price": "...",
          "features": []
        }
      },
      "vi": {
        "properties": {}
      }
    }
  ]
}
```

Normalizations after CONTENT-002:

```text
- preserve package count;
- do not invent extra packages during translation;
- exactly one package should normally have featured=true;
- preferred featured package is middle/business tier;
- preserve numerical price values unless business review asks otherwise;
- remove duplicate features;
- trim whitespace.
```

---

## 3. Individual CP JSON

Source:

```text
storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json
```

Seeder:

```text
BlockForCpDataSeeder
```

Canonical CP properties:

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

`final` is not canonical at the current schema level. Use `acticle` for final persuasive text/closing summary until a schema task adds `final`.

Skeleton:

```json
{
  "key": "proposal_key",
  "name": "Proposal Name",
  "properties": {
    "title": "...",
    "hero": {},
    "benefits": {},
    "extras": {},
    "important": {},
    "items": {},
    "includes": [],
    "acticle": "..."
  },
  "en": {
    "name": "Proposal Name",
    "properties": {
      "title": "...",
      "hero": {},
      "benefits": {},
      "extras": {},
      "important": {},
      "items": {},
      "includes": [],
      "acticle": "..."
    }
  },
  "vi": {
    "properties": {}
  }
}
```

---

## 4. Category description JSON

Source:

```text
storage/app/blocks/cat/*.json
```

Current English content fields:

```text
en.properties.descr
en.properties.content
```

Rules:

```text
- preserve category tree fields and legacy keys;
- do not remove `childs` if present;
- baseline EN category content should explain: what the service is, who it is for, and business result;
- `_blank.json` is template/hollow and should not be processed as real content.
```

---

## 5. JSON generation safety

Before writing generated JSON:

```text
- validate JSON syntax;
- preserve UTF-8;
- preserve existing keys;
- avoid raw newline/quote errors in HTML strings;
- do not add fields not present in block_item_properties unless a schema task is approved;
- keep vi placeholders empty if Vietnamese copy is not part of the task.
```
