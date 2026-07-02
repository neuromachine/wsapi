# FE-BACKEND-CONTRACT-BRIDGE — Frontend ↔ Backend Contract Bridge

## Status

Methodical bridge document between the Vue frontend and Laravel backend.

This document does not define a task.  
It stabilizes the current understanding of the API contract so that backend and frontend refactors do not silently break each other.

---

## Purpose

The WebSolutions frontend and backend are developed as separate but tightly connected systems.

The backend stores flexible EAV/content data.  
The frontend consumes flattened, UI-ready JSON.

This document defines the bridge:

```text
Laravel Resource response
  → Axios wrapper
    → Pinia store
      → page/block orchestrator
        → presentation components
```

---

## Core Rule

The frontend expects the useful payload here:

```text
response.data.data
```

This is the standard Laravel Resource envelope used by the frontend.

Backend refactoring must preserve this envelope for existing endpoints unless a coordinated migration is explicitly approved.

---

## Contract vs Internal Model

The backend internal model is EAV-like:

```text
Block
BlockCategory
BlockItem
BlockItemProperty
BlockItemPropertyValue
```

The frontend must not receive or depend on this internal structure directly.

The frontend expects logical data:

```text
category
content
sections
subcategories
blocks
item.properties
```

Therefore:

```text
EAV internals are backend implementation details.
Flattened payload is frontend contract.
```

---

## Category Endpoint Contract

Primary reference endpoint:

```text
GET /en/blocks/categories/services
```

Consumed by frontend route:

```text
/en/services
```

Current response root:

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
  children
```

Do not remove or rename these keys during backend refactoring without an explicit migration plan.

---

## `content` Zone

`content` contains the primary descriptive data for the category.

Typical fields:

```text
title
descr
content
metadata
priority
```

The frontend may use this for page intro, SEO/meta, body content, headings, and descriptive sections.

Backend refactoring must not move these fields to a different location without compatibility aliasing.

---

## `subcategories` Zone

`subcategories` is a public frontend-facing array for pages such as Services.

Typical item shape:

```text
id
slug
childs
title
descr
content
metadata
priority
```

Important notes:

```text
- slug maps from backend category key
- childs is legacy spelling but frontend-facing
- EAV fields are flattened into the same object
- priority may be used for display ordering
```

Do not remove `childs` just because `children` exists elsewhere.

---

## `children` Zone

`children` represents a structural category tree or compatibility field.

Its exact long-term role is not fully settled.

For now:

```text
- preserve it if already present
- do not assume it replaces subcategories
- do not remove it during backend read-side refactor
```

A future improvement proposal may define a cleaner distinction between `children`, `subcategories`, and `childs`.

---

## `sections` Zone

`sections` is a target zone for block output attached as named sections.

It may be empty for some category responses.

The backend currently uses `BlockAttachMap` to determine whether a block becomes:

```text
content
sections
blocks
```

Frontend code should not need to know the EAV storage model behind these zones.

---

## `blocks` Zone

`blocks` is the fallback or explicit collection of serialized blocks for the category.

It may include block-level structures that are not routed into `content` or `sections`.

Do not remove `blocks` during Resource/Repository refactoring even if `sections` or `content` appear to cover current rendering needs.

---

## Item Endpoint / Properties Contract

Item-oriented responses commonly use:

```text
data.id
data.key
data.name
data.properties
```

`properties` is a flattened object derived from EAV values.

Example section-like properties may include:

```text
hero
benefits
extras
important
items
includes
acticle
content
metadata
```

Rules:

```text
- preserve existing property keys
- do not rename acticle to article without alias/migration
- do not force frontend to understand property_values
- JSON value_type should decode into arrays/objects where expected
```

---

## Frontend Store Expectations

The frontend stores generally expect backend payloads to be already useful.

Stores may normalize shallow differences, but they should not rebuild backend EAV structure.

Expected flow:

```text
api.get(...)
  → response.data.data
    → blockStore.category / blockStore.item
      → orchestrator props
        → presentation component
```

Backend should therefore return page/category/item structures in stable logical form.

---

## Scope / Locale Bridge

Current backend route uses:

```text
/{locale}/...
```

Current frontend routing uses a broader concept:

```text
scope
```

At present, these may overlap for values such as:

```text
ru
en
vi
```

But they are not conceptually identical.

Backend tasks must not mechanically rename `locale` to `scope`.  
Frontend tasks must not assume every scope is only a language forever.

Future architecture may introduce a more explicit `section` or context model.

---

## Compatibility Rules

During refactoring, preserve:

```text
- response.data.data envelope
- existing endpoint URLs
- current public JSON keys
- category content structure
- subcategories structure
- blocks/sections/children presence
- flattened EAV fields
- legacy aliases until migration is approved
```

Do not preserve:

```text
- SQL inside Resources
- accidental layer violations
- duplicated transformation logic
- unclear private method names, if they can be safely improved
```

Compatibility does not mean preserving bad internals.  
It means preserving public behavior while improving implementation.

---

## Backend Refactor Implications

When refactoring backend read-side flow, agents should verify:

```text
- frontend route /en/services still renders
- GET /en/blocks/categories/services still has expected shape
- content and subcategories remain available
- blocks/sections/children are not accidentally removed
- EAV internals do not leak
```

If a backend improvement requires a frontend change, it should become a separate coordinated migration task, not a hidden side effect.

---

## Frontend Refactor Implications

When refactoring frontend consumers, agents should verify:

```text
- no direct dependency on property_values internals
- presentation components receive section data through props
- backend contract assumptions are localized in orchestrator/store layers
- legacy keys are wrapped/aliased intentionally if normalized
```

Frontend may add compatibility adapters, but should not obscure backend contract problems permanently.

---

## Known Compatibility Debt

Current known debt:

```text
childs vs children
subcategories vs structural children
acticle vs article
items as pricing/packages property
locale vs scope vs section
content as both HTML field and response zone
sections as named block output
```

These should be documented and migrated deliberately, not fixed opportunistically inside unrelated tasks.

---

## Regression References

Primary backend/frontend bridge regression:

```text
GET /en/blocks/categories/services
```

Primary saved payload reference:

```text
services.json
```

Additional item-style references may include individual offer payloads such as `visarun_system` when working on compred/offer pages.

---

## Stable Summary

```text
The backend may improve its internal data loading and Resource boundaries.
The frontend contract must remain stable until a coordinated migration is planned.
The bridge is response.data.data with flattened logical payloads, not EAV internals.
```
