# SERVICE-OFFERS-EN-CONTENT-CONTRACT

## Scope

This contract applies to service offer package files:

```text
storage/app/blocks/items/{categoryKey}.json
```

These files are consumed by:

```text
database/seeders/ServicesBlockSeeder.php
```

## Locale structure

The canonical structure is:

```json
{
  "properties": {
    "items": []
  },
  "en": {
    "properties": {
      "items": []
    }
  },
  "vi": {
    "properties": {}
  }
}
```

Rules:

- `ru` content lives in root `properties`.
- `en` content lives in `en.properties`.
- `vi.properties = {}` is acceptable only as a placeholder.
- Empty locale payloads must not contain broken or partial package data.

## Expected package shape

Each package item should use the existing project shape:

```json
{
  "index": 1,
  "icon": "card-text",
  "name": "Basic",
  "price": "from $500",
  "term": "2–3 weeks",
  "featured": false,
  "desc": "Short package description.",
  "features": [
    "Feature one",
    "Feature two"
  ]
}
```

## Quality rules

- Keep package count consistent with the source file unless the task explicitly asks to restructure.
- Keep `featured: true` only for the main recommended package.
- Translate meaning, not word-for-word wording.
- Preserve business logic of package progression.
- Do not change PHP seeders for content-only tasks.
- Do not rename keys.
