# AIP-FE-001 — Server-Driven UI Path

## Status

Draft.

## Type

Architecture Improvement Proposal.

This document describes a possible long-term frontend/backend direction. It is not a task file and does not authorize an immediate migration.

---

## 1. Purpose

The purpose of this AIP is to outline a future path from the current Vue page composition model toward a Server-Driven UI inspired architecture.

The project already has a backend content system capable of returning structured block/category/item data. The frontend already has component layers and data-driven rendering patterns. The next architectural step may be to make page composition itself more API-driven.

This must be treated as a future direction, not a near-term refactor requirement.

---

## 2. Current Frontend State

The frontend currently uses Vue 3 with:

```text
Vue Router
Pinia
usePageOrchestrator
blockStore(id)
View → index → presentation components
UI primitives / WS DS
Tailwind layer
Animation layer
```

Current page composition is still mostly static at the route/view level:

```text
Route
  → View.vue
    → index.vue
      → presentation components
        → UI primitives
```

Data is fetched from Laravel API endpoints and passed down via props.

---

## 3. Current Backend State Relevant to SDUI

The backend returns structured content via endpoints such as:

```text
GET /{locale}/blocks/categories/{slug}
GET /{locale}/blocks/items/{slug}
```

Category responses may contain:

```text
content
sections
subcategories
blocks
children
```

Item responses may contain:

```text
properties
```

This means the backend already influences what the frontend renders, but it does not yet fully describe page layout and component composition.

---

## 4. Target Direction

A possible future model:

```text
Route
  → PageRenderer
    → API page config
      → block registry
        → presentation component
          → UI primitives
```

The backend would return a page configuration describing which sections/blocks should appear and in what order.

Example conceptual response:

```json
{
  "meta": {},
  "page": {
    "type": "category",
    "slug": "services"
  },
  "blocks": [
    {
      "type": "hero",
      "key": "services_hero",
      "data": {}
    },
    {
      "type": "subcategories",
      "key": "services_directions",
      "data": []
    }
  ]
}
```

This is conceptual only. It is not the current API contract.

---

## 5. Why Not Now

The current backend read-side system still needs clarification.

Known unresolved topics:

```text
- category response contract
- subcategories / children / childs compatibility
- Resource vs Repository vs Assembler boundaries
- BlockAttachMap policy
- EAV transformation rules
- seed/import pipeline stability
```

Moving to SDUI before these are stable would amplify existing ambiguity.

Recommended order:

```text
1. Stabilize backend read-side contract.
2. Clarify category/item payload mapping.
3. Strengthen frontend/backend bridge documentation.
4. Introduce block registry conventions.
5. Prototype PageRenderer on a limited page.
6. Only then consider broader SDUI migration.
```

---

## 6. Benefits of Future SDUI Direction

Potential benefits:

```text
- less hardcoded page composition
- easier page variation by scope/locale/project
- stronger backend-driven content architecture
- reusable frontend block registry
- better support for multiple page types
- possible CMS-like composition layer
```

This aligns with the long-term idea of WebSolutions as a modular web platform rather than a single fixed site.

---

## 7. Risks

Main risks:

```text
- backend becomes responsible for frontend details too early
- response contracts become too generic and hard to debug
- frontend loses clear component ownership
- API begins returning UI implementation details instead of semantic blocks
- agent-generated changes become too broad
```

The SDUI path must remain semantic, not template-string-driven.

The backend should describe page structure and content intent, not raw Vue implementation.

---

## 8. Suggested Concepts

### 8.1 PageRenderer

A Vue component responsible for rendering an array of page blocks.

It should not know business logic. It should map block descriptors to registered components.

### 8.2 Block Registry

A frontend registry:

```text
block type → component
```

Example conceptual mapping:

```js
{
  hero: HeroSection,
  subcategories: ServicesSubcategories,
  rich_text: RichTextSection,
  card_collection: CardCollectionSection,
}
```

### 8.3 Semantic Block Types

Backend should return semantic block types, not component paths.

Preferred:

```text
hero
rich_text
card_collection
pricing_packages
subcategories
portfolio_grid
```

Avoid:

```text
src/components/blocks/services/presentation/info.vue
```

### 8.4 Compatibility Mode

Existing views can remain as they are while PageRenderer is prototyped on one page or one section.

Do not migrate all pages at once.

---

## 9. Relation to Backend AIPs

This AIP depends on backend work described in:

```text
AIP-BE-001-read-side-refactor.md
AIP-BE-002-category-response-contract.md
AIP-BE-003-resource-to-assembler-boundary.md
AIP-BE-005-naming-and-compatibility-layer.md
```

The frontend SDUI path should not force backend refactor prematurely. It should follow after the backend contract becomes stable enough.

---

## 10. Possible Future Stages

### Stage 1 — Registry Only

Create a frontend registry while keeping existing static views.

### Stage 2 — Section Renderer

Render only one part of a page dynamically, for example a card collection section.

### Stage 3 — PageRenderer Prototype

Use API-provided block descriptors for one low-risk page.

### Stage 4 — Backend Page Config

Introduce explicit page config endpoint or response node.

### Stage 5 — Gradual Migration

Move selected pages to dynamic composition only when stable.

---

## 11. Non-Goals

This AIP does not propose:

```text
- immediate replacement of current views
- backend returning Vue component paths
- frontend becoming a generic renderer for arbitrary HTML
- full CMS admin implementation
- changing existing category endpoint contracts now
- replacing current UI primitives or WS DS
```

---

## 12. Success Criteria

The SDUI path is successful if it eventually provides:

```text
- controlled dynamic page composition
- stable semantic block contracts
- reusable frontend block registry
- preserved existing pages during migration
- backend/frontend responsibilities remain clear
```

---

## 13. Failure Criteria

This path fails if:

```text
- it becomes a premature rewrite
- backend returns frontend implementation details
- API contracts become harder to reason about
- existing pages break during migration
- every block becomes a special case
- content semantics are replaced by layout hacks
```

---

## 14. Current Recommendation

Do not implement SDUI yet.

Use this AIP as a long-term orientation layer while the project first stabilizes:

```text
- backend read-side flow
- category response contract
- EAV mapping
- naming compatibility
- frontend/backend bridge
```

After that, prototype a small frontend block registry before attempting PageRenderer.
