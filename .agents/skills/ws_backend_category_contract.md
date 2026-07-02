# Skill: WS Backend Category Endpoint Contract

## Use when

Use this skill for work on category endpoints, especially:

```text
GET /{locale}/blocks/categories/{slug}
GET /en/blocks/categories/services
```

Primary references:

- `.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md`
- `.agents/info/BE-00-overview.md`
- `.agents/info/BE-RESOURCE-BOUNDARY.md`

## Control endpoint

```text
GET /en/blocks/categories/services
```

Frontend route depending on it:

```text
/en/services
```

Current lifecycle:

```text
Frontend /en/services
  → API /en/blocks/categories/services
  → BlockCategoryController
  → BlockCategoryRepository::getCategory('en', 'services')
  → BlockCategoryResource
  → Vue services page
```

## Current public response shape

The response payload must preserve the existing outer shape:

```text
response.data.data
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

For the first backend refactor, do not rename or remove these keys.

## Important fields

### content

Category-level denormalized content, currently derived from `descr_data`.

Expected meaning:

```text
data.content.title
data.content.descr
data.content.content
data.content.metadata
data.content.priority
```

### subcategories

Working public contract for services page category cards.

Expected item shape:

```text
subcategories[]
  id
  slug
  childs
  title
  descr
  content
  metadata
  priority
```

`subcategories` must remain present for `/en/services`.

### childs

`childs` is legacy spelling in the current public JSON. Do not rename it to `children` during a surgical backend pass unless the task explicitly covers frontend migration.

### children

`children` is a structural/recursive field and must not be removed in the first pass. Its exact long-term role is unresolved.

### sections / blocks

Preserve both keys even if one is empty for the control endpoint.

## Compatibility rule

```text
The first backend pass may improve internals but must not change the response shape consumed by frontend.
```

Do not change:

```text
- endpoint URL
- locale route segment
- public JSON key names
- subcategories availability
- childs spelling
- content/sections/blocks root keys
```

## Allowed internal changes

```text
- move data preparation from Resource to Repository
- eager-load relations needed by Resource
- add private Repository helpers for readability
- add a very small read assembler only if it reduces complexity and does not become a new hidden Resource
```

## Forbidden in first pass

```text
- route changes
- database schema changes
- API shape redesign
- locale → scope rename
- subcategories → children rename
- childs → children rename
- frontend changes
- BlockAttachMap redesign
```
