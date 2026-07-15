# BE-12 — API Data Lift and Resource Flow

## Purpose

Describe how seeded EAV data is “lifted” from database rows into frontend-facing JSON.

---

## 1. Read-side architecture

Current intended backend flow:

```text
Route
  -> Controller
    -> Repository
      -> prepared Eloquent model graph
        -> CategoryPayloadAssembler / EavContentResolver / BlockAttachMap
          -> Resource
            -> JSON response
```

Principles:

```text
- Controller is thin.
- Repository prepares model graph and applies locale filtering.
- Resource serializes, not queries.
- EavContentResolver transforms EAV rows to flat arrays.
- BlockAttachMap defines current compatibility placement policy.
```

---

## 2. Standard category endpoint

```text
GET /api/{locale}/blocks/categories/{slug}
```

Typical response shape:

```json
{
  "data": {
    "id": 2,
    "key": "services",
    "name": "Services",
    "description": null,
    "content": {},
    "parent_id": null,
    "section": "en",
    "sections": [],
    "subcategories": [],
    "blocks": []
  }
}
```

Known compatibility keys:

```text
data
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
children / childs depending on nested payload
```

---

## 3. Offers endpoint

```text
GET /api/{locale}/blocks/categories/offers/{proposalKey}
```

Current public shape is flat, not wrapped in `data`:

```json
{
  "category": {},
  "block": {},
  "items": []
}
```

This endpoint is used for individual commercial proposal rendering.

Do not change to standard Resource envelope until frontend handoff is explicitly planned.

---

## 4. EAV lifting

Internal DB rows:

```text
BlockItem
  propertyValues[]
    property.key
    property.type
    property.is_collection
    value
    value_type
    locale
```

Frontend-facing object:

```json
{
  "title": "...",
  "hero": {},
  "items": [],
  "important": {}
}
```

The transform is centralized through `EavContentResolver`.

---

## 5. `EavContentResolver`

Role:

```text
EAV property values -> typed flat object
```

Current expected behaviors:

```text
- decode JSON values when value_type/property type indicates JSON;
- cast booleans/numbers when applicable;
- group values into arrays when property.is_collection is true;
- sort item collections if a `sort` property exists;
- expose a single-item resolution path for BlockItemResource consistency.
```

---

## 6. `CategoryPayloadAssembler`

Role:

```text
prepared category model + loaded blocks/items -> public category payload sections
```

It preserves legacy compatibility and determines what becomes:

```text
content
sections
subcategories
blocks
children/childs compatibility
```

---

## 7. `BlockAttachMap`

Role:

```text
compatibility policy for attaching blocks to response zones
```

Examples of current conceptual mapping:

```text
descr_data -> content
slide -> sections
list/simplehtml/etc. -> mapped response zones depending on config
```

This is currently code-level policy. Moving it to database metadata is future work, not part of content JSON production.

---

## 8. Frontend boundary

Frontend consumes:

```text
response.data.data for standard Resource endpoints
flat object for offers endpoint
```

Frontend should not consume EAV internals.

Fields that are effectively frontend contract:

```text
category/subcategory:
  id, slug/key, title/name, descr, content, metadata, priority, childs

service offers:
  items array, price, term/timeline, features, featured, icon, name/title, desc/descr

individual CP:
  hero, benefits, extras, important, items, includes, acticle
```

---

## 9. Contract risk checklist

Before changing backend read-side code, check:

```text
- Does the endpoint keep the same envelope shape?
- Are legacy keys preserved?
- Are EAV values still flattened?
- Are JSON values decoded?
- Is locale filtering still applied?
- Does `offers` endpoint remain flat?
- Does frontend still get `response.data.data` where expected?
```
