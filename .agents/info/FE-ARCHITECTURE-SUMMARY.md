# FE-ARCHITECTURE-SUMMARY — Frontend Architecture Summary

## Status

Methodical frontend architecture summary for the WebSolutions project.

This document does not define a new task.  
It summarizes the current frontend architecture so that future backend, frontend, and agentic refactoring tasks can stay aligned with the existing Vue application structure.

---

## Purpose

The frontend of WebSolutions is a Vue SPA that consumes a Laravel API.  
Its current architecture is already more structured than the backend read-side layer and should be treated as an existing contract, not as an experimental area during backend refactoring.

This document answers:

```text
How is the frontend organized?
Where does data enter?
Which layer owns fetch/store logic?
Which components must remain presentational?
What backend response shape does the frontend rely on?
```

---

## Stack

```text
Vue 3
Vite
Vue Router 4
Pinia
Axios wrapper: src/utils/api.js
vue-i18n + i18n sync plugin
Bootstrap — transitional layout/styling layer
Tailwind CSS v4 — design-system engine / semantic utility layer
GSAP + ScrollTrigger — animation layer
Laravel API backend — external data source
```

---

## Core Frontend Principle

The frontend is structured around separated responsibilities:

```text
View composition
  → page/block orchestration
    → presentational sections
      → UI primitives / micro components
```

The key rule:

```text
Data fetching and store ownership must stay in orchestration layers.
Presentation components receive data through props.
```

---

## Component Layers

### 1. View Layer

Typical files:

```text
src/views/Services.vue
src/views/Compred.vue
src/views/Portfolio.vue
src/views/Group.vue
src/views/ServiceView.vue
```

Role:

```text
- compose page-level layout
- mount header/footer/page title/index components
- stay mostly free of data-fetching logic
- avoid direct backend assumptions where possible
```

The view layer should not become the place where API payloads are repaired.

---

### 2. Block Orchestrator Layer

Typical files:

```text
src/components/blocks/services/index.vue
src/components/blocks/portfolio/index.vue
src/components/blocks/compred/index.vue
```

Role:

```text
- own block/page store interaction
- call usePageOrchestrator where appropriate
- determine what data is passed to child presentation components
- normalize only frontend-local concerns
- keep backend response contract visible but not deeply reinterpreted
```

This is the frontend layer most sensitive to backend JSON changes.

---

### 3. Presentation Layer

Typical files:

```text
src/components/blocks/services/presentation/*.vue
src/components/blocks/portfolio/presentation/*.vue
src/components/blocks/compred/presentation/*.vue
```

Role:

```text
- receive data through props
- render sections/cards/lists
- emit UI events where necessary
- avoid direct store imports
- avoid API calls
- avoid route-level assumptions unless explicitly part of link rendering
```

Presentation components should not compensate for unstable backend shape by adding complex defensive mapping everywhere.

---

### 4. UI Primitive / Design System Layer

Typical files:

```text
src/components/blocks/general/ui/card.vue
src/components/blocks/general/ui/SectionHeader.vue
src/components/blocks/general/ui/RichText.vue
```

Role:

```text
- provide reusable layout and visual primitives
- define slot-based rendering contracts
- avoid business knowledge
- avoid direct dependency on specific block types
```

A Card should not know about services, offers, benefits, categories, or EAV.

---

## Stores

### uiStore

Role:

```text
- global UI state
- current scope / URL prefix
- page variables
- global loading counter
- page title / breadcrumbs / parent / children
```

Important concept:

```text
scope is a frontend routing context.
It is not always semantically identical to backend locale.
```

---

### navigationStore

Role:

```text
- navigation structure
- navbar links
- non-blocking navigation fetch
```

Navigation fetch should not block the initial render unless explicitly required.

---

### blockStore(id)

Role:

```text
- factory-based store per block ID
- category data
- item data
- overlay data
- filter state
- fetchBlockCategory()
- fetchBlockItem()
- fetchOverlayCategory()
```

Important rule:

```text
blockStore is not a singleton.
The store definition is cached by ID to avoid duplicate Pinia instances.
```

This affects every agentic refactor touching fetch logic.

---

### formStore

Role:

```text
- form submission state
- validation errors
- submit lifecycle
```

The form system is separate from Blocks/EAV read-side refactoring.

---

## usePageOrchestrator

`usePageOrchestrator` centralizes page/block data fetching.

Typical responsibilities:

```text
- determine requested slug
- call category/item/structure fetch methods
- prevent duplicate fetches
- determine whether current block is page owner
- coordinate global loading state
- optionally build page variables
```

Known schemes:

```text
category
item
structure
structure+category
structure+category+item
```

Important rule:

```text
Only the page owner should call buildPageVars.
Nested blocks must not overwrite global page variables.
```

---

## API Consumption Pattern

The frontend expects Laravel Resource responses under:

```text
response.data.data
```

This is a core bridge rule.

The backend must not suddenly return raw model arrays or remove the `data` envelope for existing endpoints.

---

## Services Page Context

The services page is currently one of the most important contract consumers.

Route:

```text
/en/services
```

Backend request:

```text
/en/blocks/categories/services
```

The frontend consumes category response zones such as:

```text
content
subcategories
blocks
sections
children
```

For this reason, backend refactors around `BlockCategoryResource`, `BlockCategoryRepository`, or EAV mapping must preserve response compatibility.

---

## Compred / Individual Offer Context

The compred-style page consumes item-oriented payloads with a shape similar to:

```text
data.id
data.key
data.name
data.properties
```

Inside `properties`, sections may include:

```text
hero
benefits
extras
important
items
includes
acticle / article compatibility
```

Presentation sections should receive only the relevant section node, not the entire root payload unless justified.

---

## Tailwind / Design System Context

Tailwind is used as a design-system engine, not as random inline styling.

Rules:

```text
- prefer semantic tokens
- avoid raw hex in templates
- do not duplicate theme values between CSS and config
- Bootstrap may remain during transition
- avoid broad Tailwind migration during backend tasks
```

Backend work should not modify frontend styling.

---

## Animation Context

Animations are handled through a dedicated animation layer.

Rules:

```text
- GSAP logic should be decoupled from component business logic
- use orchestrator/composable pattern
- avoid direct gsap calls in random mounted hooks
- backend/API refactors should not touch animation files
```

---

## Frontend Risks During Backend Refactor

The main risks are:

```text
- renaming response keys that presentation components expect
- removing legacy keys such as childs
- changing content/subcategories/blocks structure
- changing response.data.data envelope
- moving category data into a different nested node
- returning EAV internals instead of flattened fields
- confusing backend locale with frontend scope
```

---

## What Backend Agents Must Remember

During backend read-side refactoring, the frontend should be treated as a compatibility consumer.

Do not assume that ugly response names are safe to rename.

Do not assume that `children`, `subcategories`, and `childs` are equivalent at runtime.

Do not change the frontend to fit a backend refactor unless the task explicitly authorizes a coordinated frontend/backend migration.

---

## Stable Summary

```text
Frontend is structured around orchestration + presentational rendering.
Backend must preserve response.data.data and current endpoint shapes.
The services category endpoint is a key regression reference.
Backend read-side improvements must reduce internal complexity without forcing frontend rewrites.
```
