# Skill: WS Frontend Backend Contract

## Purpose

Use this skill when backend changes can affect Vue frontend data consumption, or when frontend changes depend on Laravel API response shapes.

This project treats the API response shape as a public contract between backend and frontend. Do not break it casually.

---

## Core Rule

The frontend consumes logical payloads, not database structures.

```text
Backend may store EAV.
Frontend must receive stable JSON objects.
```

Do not leak backend internals into Vue components.
Do not rename frontend-facing fields without compatibility planning.

---

## Standard API Envelope

The Vue frontend expects Laravel Resource responses through:

```js
response.data.data
```

Do not remove the `data` envelope unless explicitly requested as a breaking API migration.

---

## Key Reference Flow

Reference frontend route:

```text
/en/services
```

Reference backend endpoint:

```text
GET /en/blocks/categories/services
```

This endpoint feeds the services page and is a primary regression target for backend read-side refactors.

---

## Category Endpoint Contract

Preserve root fields unless a separate migration task approves changes:

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

Preserve subcategory compatibility fields:

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

Some names are imperfect, but they are currently part of compatibility.

---

## Item / Properties Contract

For item endpoints and block item payloads, frontend expects a denormalized object structure such as:

```text
id
key/name/slug as relevant
properties
```

Inside `properties`, frontend expects logical keys, not EAV internals:

```text
title
descr
content
metadata
price
features
hero
benefits
includes
items
important
extras
```

Do not expose:

```text
property_id
property_value_id
pivot ids
raw EAV relation names
```

unless a task explicitly requests a debug/admin endpoint.

---

## Frontend Architecture Reminder

Frontend layers:

```text
View.vue
  layout only

index.vue
  orchestration, stores, usePageOrchestrator

presentation components
  props only, no direct store/API

UI primitives
  generic rendering, no business data fetching
```

Backend changes must not force presentation components to understand EAV.

---

## Locale / Scope / Section Warning

Current backend route uses `{locale}`.
Frontend increasingly thinks in terms of `scope`.
Future architecture may introduce broader `section` semantics.

Do not mechanically rename:

```text
locale → scope
scope → section
```

Preserve existing route behavior unless the task explicitly defines a migration.

---

## Legacy Compatibility Names

Known compatibility debt:

```text
childs
acticle
items
locale/section/scope mismatch
```

Do not “fix” these names in a backend refactor unless the task explicitly requires a compatibility alias layer.

Safe approach:

```text
- preserve old key
- optionally add new alias only if approved
- document future migration
```

---

## Before Changing Backend Response Shape

Ask through analysis/report, not by stopping unnecessarily:

```text
1. Which frontend file consumes this field?
2. Is this field present in reference payloads?
3. Is this field part of .agents/info contract docs?
4. Can the change be implemented as additive alias instead of rename?
5. What manual regression confirms compatibility?
```

If unsure, preserve the field.

---

## Safe Backend Refactor Behavior

Allowed:

```text
- change internal query structure
- change Resource internals
- add assembler/helper classes
- improve eager loading
- reduce duplication
```

Only if the output remains compatible.

Forbidden without explicit task approval:

```text
- remove public keys
- rename public keys
- change `data` envelope
- force frontend EAV awareness
- change route URLs
- change locale/scope behavior
```

---

## Final Report Requirements

For any change crossing frontend/backend contract, report:

```text
- affected endpoint(s)
- affected frontend route/component if known
- response shape preserved/changed
- compatibility fields preserved
- manual regression performed
- remaining naming/contract debt
```
