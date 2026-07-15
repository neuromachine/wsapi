# Contract — Individual Commercial Proposals (`ind_offers`)

## Scope

This contract defines the JSON file shape for individual commercial proposal content.

Use this for files under:

```text
storage/app/blocks/blocks/items/ind_offers/{key}.json
```

Seeder:

```text
php artisan db:seed --class=BlockForCpDataSeeder
```

Endpoint:

```text
GET /api/{locale}/blocks/categories/offers/{key}
```

---

## Required root fields

```json
{
  "key": "unique-slug",
  "name": "Human readable name",
  "block": "ind_offers",
  "properties": {},
  "en": { "name": "...", "properties": {} },
  "vi": { "name": "...", "properties": {} }
}
```

Rules:

```text
key    — stable slug, lowercase latin, hyphen/underscore only
name   — admin/front label
block  — must be ind_offers
properties — RU/default payload
```

Non-RU locale branches are optional, but when present must use:

```json
"en": { "name": "...", "properties": { ... } }
```

Do not put localized properties at root except for RU/default.

---

## Allowed property keys

Current canonical keys supported by `BlockForCpDataSeeder`:

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

Do not invent keys such as `final`, `cta`, `faq`, `timeline`, `seo` unless a backend/schema task explicitly adds them.

### Legacy spelling

`acticle` is misspelled but canonical in the current DB/seeder contract. Do not rename it to `article` in content JSON.

---

## Property meanings

### title

Short label. Often empty if UI uses item `name`, but can be populated.

### content

Optional HTML/plain content field. Keep empty unless a specific UI block consumes it.

### acticle

Main closing/persuasive summary. Use this for the final commercial statement until a dedicated `final` property exists.

### hero

Object:

```json
{
  "pretitle": "...",
  "title": "...",
  "focus": "...",
  "paragraph": "..."
}
```

### benefits

Object:

```json
{
  "pretitle": "...",
  "title": "...",
  "items": [
    { "index": 1, "icon": "...", "title": "...", "text": "..." }
  ]
}
```

Use 4–5 items.

### extras

Business result / measurable effect.

```json
{
  "pretitle": "...",
  "title": "...",
  "items": [
    { "index": 1, "icon": "check-lg", "title": "+35%", "text": "..." }
  ]
}
```

Use 3–5 items. Do not fake impossible precision. Use plausible ranges.

### important

Functional/system detail. Use when the offer is a system, platform, booking flow, CRM, automation, AI assistant, etc.

```json
{
  "pretitle": "...",
  "title": "...",
  "items": [
    { "index": 1, "icon": "...", "title": "...", "text": "<ul><li>...</li></ul>" }
  ]
}
```

Use 2–5 blocks. HTML inside `text` is allowed, but keep it simple and valid.

### items

Pricing packages.

```json
{
  "pretitle": "Стоимость",
  "title": "Пакеты внедрения",
  "items": [
    {
      "index": 1,
      "icon": "card-text",
      "name": "Базовый",
      "price": "34 000 ₽",
      "term": "2–3 недели",
      "featured": false,
      "desc": "...",
      "features": ["..."]
    }
  ]
}
```

Rules:

```text
- usually 3 packages
- index 1/2/3
- package 2 is usually featured=true
- each next package grows logically
- 6–10 features per package
- price and term must be plausible for WS production effort
```

### includes

Array of shared included values:

```json
[
  { "index": 1, "text": "Анализ текущих процессов", "icon": "check-lg" }
]
```

Use 3–5 items.

---

## Locale rules

### RU/default

Use root `properties`.

### EN

Use:

```json
"en": { "name": "...", "properties": { ... } }
```

### VI

Vietnamese can be either full or hollow placeholder:

```json
"vi": { "name": "...", "properties": {} }
```

A hollow placeholder is acceptable only when the task explicitly does not require Vietnamese content. The seeder will not create hollow property values because there are no properties to iterate.

---

## Validation checklist

Before saving a generated file:

```text
- valid JSON
- root key matches filename
- block = ind_offers
- root properties exists
- each non-empty locale branch uses { name, properties }
- no unsupported property keys
- hero has pretitle/title/focus/paragraph
- benefits.items has index/icon/title/text
- items.items has 3 packages unless task says otherwise
- includes is an array, not an object
- no markdown inside JSON strings unless the consuming UI expects it
- HTML snippets are simple and safe
```

