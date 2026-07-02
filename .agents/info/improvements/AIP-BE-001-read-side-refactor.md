# AIP-BE-001 — Backend Read-Side Refactor

## Status

Draft.

## Scope

This proposal covers the backend read-side data loading and response assembly system for the Blocks/Categories module.

Primary focus:

```text
GET /{locale}/blocks/categories/{slug}
GET /en/blocks/categories/services
```

Primary files involved conceptually:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Models/BlocksCategories.php
app/Models/Block.php
app/Models/BlockItem.php
app/Models/BlockItemProperty.php
app/Models/BlockItemPropertyValue.php
```

This proposal does not cover frontend refactor, database redesign, or seeder refactor directly.

Seeder/import improvements are planned as a separate AIP and optional task.

---

## Core Problem

The project has a powerful and flexible backend content model, but the current read-side flow has accumulated complexity around “подъем данных” — lifting category, block, item, EAV, localized property values, sections, subcategories, and compatibility keys into a final frontend-ready JSON response.

The current system works, but parts of the response assembly appear to be distributed across layers in a way that makes future evolution harder:

```text
Repository partially prepares model graph.
Resource serializes, but may also compensate for missing relations.
EavContentResolver flattens EAV items.
BlockAttachMap decides where blocks land in the response.
Frontend consumes a compatibility-shaped payload.
```

The risk is not one isolated method. The risk is that future features will continue to add small local patches until the category endpoint becomes too fragile to evolve.

---

## Background

The system is based on a headless content/API approach:

```text
Laravel API
  → Vue SPA
```

The data model is EAV-like:

```text
blocks                  — logical block/entity type
blocks_categories       — hierarchical placement/taxonomy
block_items             — entity instances
block_item_properties   — dynamic schema fields
block_item_property_values — localized values
```

The frontend expects Laravel Resource envelope semantics:

```text
response.data.data
```

For the services page, the important reference chain is:

```text
Frontend route:
  /en/services

API request:
  /en/blocks/categories/services

Backend flow:
  BlockCategoryController
    → BlockCategoryRepository
      → BlockCategoryResource
        → EavContentResolver
        → BlockAttachMap
        → JSON response
```

---

## Current Public Contract Pressure

The public response shape for category endpoints is already consumed by frontend components.

For `/en/blocks/categories/services`, the response must preserve key areas such as:

```text
data.content
data.sections
data.subcategories
data.blocks
data.children
```

Inside `subcategories`, compatibility keys currently matter:

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

Some names are imperfect, but they are still public contract until a migration plan exists.

Examples of compatibility debt:

```text
childs vs children
subcategories vs children
locale vs scope vs section
acticle vs article
items as both block items and payload items
```

These should be documented and eventually migrated through compatibility layers, not renamed casually.

---

## Why the Earlier Narrow Patch Was Not Enough

An earlier task focused on a narrow issue:

```text
Remove SQL from BlockCategoryResource::resolveSubitems()
```

The direction was valid, because Resource should not perform SQL.

However, the task was too narrow in relation to the real architectural problem. It assumed the correct fix was simply to extend eager loading and keep the rest of the shape untouched.

That may be useful as a first step, but it does not answer larger questions:

```text
- Who owns response assembly?
- Is Repository becoming too large?
- Should category payload construction live in Resource or a dedicated builder?
- Which fields are legacy but public?
- What should happen to children/subcategories/childs naming?
- How do BlockAttachMap and EavContentResolver participate in a clean pipeline?
- How can the system grow without accumulating more local patches?
```

The conclusion: future refactoring should be broader in analysis, even if implementation remains conservative.

---

## Current Responsibility Model

The intended model is:

```text
Controller
  → receives route params
  → calls backend data layer
  → returns Resource/response

Repository / Query Layer
  → SQL
  → Eloquent query construction
  → eager loading
  → locale filtering
  → relation completeness
  → prepared model graph

Resource
  → public response shape
  → serialization
  → compatibility keys
  → no DB queries

EavContentResolver
  → EAV items to flat objects
  → value_type casting
  → is_collection handling
  → single/keyed/array modes

BlockAttachMap
  → current compatibility policy for routing blocks into content/sections/blocks
```

The observed problem is that the actual implementation may blur Repository and Resource responsibilities when the Resource needs data that the Repository did not fully prepare.

---

## Design Goal

Move toward a read-side pipeline that is explicit, inspectable, and compatible:

```text
Route
  → Controller
    → Query / Repository
      → Prepared Category Graph
        → Response Assembly Layer
          → Resource Serialization
            → JSON Contract
```

The “Response Assembly Layer” may be implicit at first or explicit later.

The important part is to avoid hidden query work inside serialization.

---

## Constraints

### Must Preserve

```text
- working frontend behavior
- current endpoint URLs
- Laravel Resource envelope
- services.json-compatible shape for /en/blocks/categories/services
- current locale behavior
- current EAV output semantics
- compatibility keys such as childs, subcategories, children
```

### Must Avoid Initially

```text
- database schema changes
- route changes
- frontend changes
- forced rename migrations
- broad rewrite of all Resources
- premature DB-driven replacement of BlockAttachMap
- mixing read-side refactor with seed/import refactor
```

---

## Improvement Options

### Option A — Improved Repository + Resource Boundary

Description:

Keep the current Repository and Resource structure, but enforce a stricter boundary.

Repository prepares the complete category graph. Resource serializes only what is already available.

Potential changes:

```text
- stronger eager loading for child category data
- no SQL in Resources
- explicit relationLoaded handling
- clearer private methods inside Resource
- better naming inside code while preserving public keys
```

Pros:

```text
- minimal architectural change
- low-risk
- easy to review
- preserves current mental model
- good first stabilization step
```

Cons:

```text
- Repository may become heavier
- Resource may still remain too aware of response assembly
- does not solve naming debt
- may not scale well for more complex category/page payloads
```

Best use:

```text
Short-term stabilization and cleanup.
```

---

### Option B — Dedicated Category Payload Builder / Assembler

Description:

Introduce a small class responsible for assembling the category response payload from an already-loaded model graph.

Possible names:

```text
App\Support\CategoryPayloadBuilder
App\Support\BlockCategoryPayloadBuilder
App\Services\Blocks\CategoryResponseAssembler
App\ReadModels\BlockCategoryReadModelBuilder
```

Possible pipeline:

```text
Repository returns prepared category model.
Assembler builds content/sections/subcategories/blocks/children arrays.
Resource wraps/serializes the final payload.
```

Pros:

```text
- prevents Resource from becoming a large assembler
- makes category response construction explicit
- easier to test and reason about
- better fit for complex read-side payloads
```

Cons:

```text
- introduces a new layer
- risk of simply moving messy Resource code into another class
- requires clear naming and responsibility discipline
```

Best use:

```text
When category response assembly becomes too large for Resource but does not belong in Repository.
```

---

### Option C — Read Model Layer

Description:

Introduce a read-side layer that prepares API-specific structures separately from Eloquent models.

Possible direction:

```text
CategoryReadModel
CategoryPayload
SectionPayload
SubcategoryPayload
```

Pros:

```text
- clear separation between DB model and API output
- strong future direction for SDUI-like architecture
- reduces accidental Eloquent leakage into response contracts
```

Cons:

```text
- larger change
- may be too early for current project size
- requires stricter tests/regression checks
```

Best use:

```text
Later, when endpoint contracts stabilize and more pages require similar composition.
```

---

### Option D — Contract-First API Layer

Description:

Formalize endpoint response contracts first, then refactor implementation behind those contracts.

This does not necessarily require OpenAPI immediately. It may start with Markdown contracts and JSON examples.

Pros:

```text
- reduces accidental frontend breakage
- good for agentic refactoring
- forces clarity before code edits
```

Cons:

```text
- documentation overhead
- may slow initial refactor
```

Best use:

```text
Should be used alongside all other options.
```

---

## Recommended Direction

Recommended staged direction:

```text
1. Accept Option D as a permanent discipline: contract-first before refactor.
2. Use Option A for immediate stabilization where safe.
3. Evaluate Option B for category endpoint response assembly.
4. Postpone Option C until the system clearly needs formal read models.
```

In practical terms:

```text
Do not jump straight to a big rewrite.
Do not continue patching single methods blindly.
Introduce a broader analysis-first refactor task.
Allow an assembler only if the code clearly benefits from it.
Preserve the current response shape.
```

---

## Proposed Future Task Direction

A future task derived from this AIP should not say only:

```text
Remove SQL from BlockCategoryResource.
```

It should say:

```text
Analyze and refactor the Blocks/Categories read-side data loading and response assembly system.
Improve layer boundaries and reduce hidden data lifting while preserving the current public API shape for /en/blocks/categories/services.
```

This became the conceptual basis for:

```text
TASK-BE-002 — Complex Refactor of Blocks Read-Side Data Loading System
```

---

## Acceptance Criteria for Any Future Refactor

A future refactor based on this AIP should be accepted only if:

```text
- the response shape remains compatible with existing frontend consumers
- Resources do not perform direct SQL queries
- EAV transformation remains centralized and predictable
- BlockAttachMap compatibility behavior is preserved unless separately changed
- category/subcategory output is easier to understand
- new abstractions are justified by reduced complexity
- manual or automated regression confirms /en/blocks/categories/services still works
```

---

## Warning Signs

Reject or revise a refactor if it:

```text
- breaks services.json-compatible response shape
- renames public keys because they look ugly
- introduces a large service layer without reducing complexity
- changes DB schema during read-side cleanup
- mixes read-side refactor with seeder refactor
- moves SQL from Resource into another poorly named dumping ground
- makes the final code harder to follow
- claims success without endpoint comparison or clear regression notes
```

---

## Open Questions

These should be discussed before a major implementation pass:

```text
1. Should category response assembly remain in Resource or move to a builder?
2. Which endpoint response fields are truly stable public contract?
3. Should `children` and `subcategories` eventually become separate formal concepts?
4. Should `childs` be preserved forever, aliased, or migrated later?
5. How much sorting should happen in Repository vs Resolver vs Resource?
6. Should BlockAttachMap remain code policy or migrate to DB metadata later?
7. What is the minimum regression suite required before large backend refactors?
```

---

## Relation to Seeder Refactor

Seeder/import refactor is related but separate.

Read-side refactor answers:

```text
How does the API load and return data?
```

Seeder refactor answers:

```text
How does content enter the database?
```

Do not mix both in one implementation task unless explicitly accepted.

---

## Relation to Frontend / SDUI

The frontend is moving toward cleaner component contracts and potentially Server-Driven UI.

This AIP does not implement SDUI.

However, a cleaner backend read-side response layer is a prerequisite for future SDUI-like page configuration because the backend must be able to provide structured, stable, inspectable payloads.

---

## Final Recommendation

The next architecture work should proceed in this order:

```text
1. Stabilize and document category endpoint contract.
2. Run a broad analysis-first backend read-side refactor.
3. Preserve current response shape.
4. Decide whether a small CategoryPayloadBuilder is justified.
5. Only later address naming migration and DB-driven attach metadata.
```

The guiding principle remains:

```text
Improve without breaking.
```

The implementation should make the system more understandable under real project constraints, not theoretically perfect.
