# WebSolutions Architecture Glossary

## Purpose

This glossary stabilizes terminology used across WebSolutions architecture materials, frontend/backend documentation, agent skills, and future tasks.

The project contains several transitional terms and legacy names. Some are imperfect but currently part of working contracts.

This document does not rename anything by itself.

It explains current meaning, target direction when known, and compatibility risk.

---

# Core Project Terms

## WebSolutions / WS

The project and business context for the system.

In architecture materials, WS usually means the WebSolutions web platform and its surrounding engineering approach.

---

## WS CODE

Project-context name for the architecture/code-generation work around WebSolutions.

Used when discussing code-agent workflows, `.agents` materials, and systematic refactoring.

---

## Architecture Materials

Markdown documentation describing how the system works and how future code agents should understand it.

These are not execution tasks.

Examples:

```text
SYSTEM.md
CA.md
DM.md
BE-00-overview.md
BE-RESOURCE-BOUNDARY.md
```

---

## Agent Runtime Materials

Files used by Codex / Antigravity / other code agents to guide execution.

Examples:

```text
AGENTS.md
.agents/agents.md
.agents/skills/*.md
.agents/tasks/*.md
```

---

## AIP — Architecture Improvement Proposal

A planned document type for discussing possible improvements before creating tasks.

AIP is not a code task.

AIP should describe:

```text
- problem
- options
- risks
- decision criteria
- recommended direction
- migration constraints
```

---

# Frontend Terms

## Frontend

Vue 3 SPA layer using Vue Router, Pinia, Vite, Tailwind, and GSAP.

Consumes Laravel API responses through Axios.

---

## View.vue

Top-level page composition component.

Expected role:

```text
layout shell only
```

Should not:

```text
- import stores
- fetch data
- perform page orchestration
```

---

## index.vue

Block/page orchestrator component.

Expected role:

```text
- use usePageOrchestrator
- own stores for the page/block
- pass data down via props
```

---

## presentation component

Feature-level Vue component responsible for rendering a section of UI from props.

Should not import stores directly.

Example:

```text
src/components/blocks/compred/presentation/benefits.vue
```

---

## item component

Lowest-level presentational component, ideally pure props.

Should not know route, store, API, or global page state.

---

## WS DS

WebSolutions Design System.

Describes UI primitives, component composition rules, and reusable interface building blocks.

Key principle:

```text
composition over configuration
```

---

## UI primitive

Reusable design-system component with minimal business knowledge.

Examples:

```text
Card
SectionHeader
Button
RichText
```

Should not know backend EAV, routes, blockStore, or business-specific sections.

---

## Tailwind Theme Layer

Frontend styling layer using Tailwind as a design-system engine.

Expected direction:

```text
tokens → semantic classes → usage
```

Avoid raw hex values in templates unless explicitly justified.

---

## Animation Layer

GSAP/Pinia-based animation orchestration layer.

Key terms:

```text
animationStore
PAGE_ENTER
PAGE_LEAVE
useGsapOrchestrator
gsap.context()
```

Animation should be decoupled from data fetching and business logic.

---

## blockStore

Pinia factory store for block-scoped data.

Important:

```text
blockStore is not a singleton.
```

It is created per ID to isolate data between blocks.

---

## usePageOrchestrator

Frontend composable that connects route, scope, store, and fetch strategy.

Known schemes:

```text
category
item
structure
structure+category
structure+category+item
```

---

## page owner / isPageOwner

The frontend concept that only one orchestrator should own global page vars for a route.

Prevents child blocks from overwriting PageTitle/breadcrumbs.

---

# Backend Terms

## Backend

Laravel API layer serving structured content and form endpoints to the Vue SPA.

---

## Laravel Resource Envelope

Standard Laravel API Resource wrapping response data inside:

```json
{
  "data": {}
}
```

Frontend expects actual payload at:

```js
response.data.data
```

---

## Controller

Laravel HTTP controller.

Expected role:

```text
- receive request / route params
- call repository/service
- return resource/response
```

Should stay thin.

---

## Repository

Backend layer responsible for preparing data from the database.

Expected responsibilities:

```text
- SQL / Eloquent query construction
- eager loading
- locale filtering
- relation completeness
- returning prepared model graph
```

Core rule:

```text
Repository prepares.
```

---

## Resource

Laravel JsonResource responsible for serializing already-prepared data into API shape.

Expected responsibilities:

```text
- explicit response array
- formatting
- compatibility keys
- calling pure mappers/resolvers on loaded data
```

Should not:

```text
- perform SQL
- call Model::where()
- call Repository
- perform hidden eager loading
```

Core rule:

```text
Resource serializes.
```

---

## Read-side

The part of backend responsible for reading content from DB and returning API responses.

Includes:

```text
Controller
Repository
Model relations
Resource
EavContentResolver
BlockAttachMap
```

Does not include seed/import pipeline unless explicitly stated.

---

## Data lifting / подъем данных

Project-specific shorthand for the process of gathering related DB/EAV/category/block data and shaping it into frontend-ready API payload.

Problem area:

```text
when this lifting is scattered across Repository, Resource, helper classes, and hidden model queries
```

---

## Prepared model graph

An Eloquent model with all relations needed by Resource already loaded and filtered.

Example:

```text
category
  blocks
    items
      propertyValues
        property
  children
    items
      propertyValues
        property
```

A Resource should receive this, not build it itself.

---

## Assembler / Payload Builder / Read Model Builder

Potential future layer that assembles a response-ready structure between Repository and Resource.

Not currently mandatory.

Useful when Resource becomes too smart and Repository becomes too broad.

Must not become a new dumping ground.

---

# EAV Domain Terms

## EAV

Entity–Attribute–Value model.

Allows dynamic content schema without database migrations for each new content field.

In this project:

```text
Entity    → BlockItem
Attribute → BlockItemProperty
Value     → BlockItemPropertyValue
```

---

## Block

Logical content/entity type.

Database table:

```text
blocks
```

Examples may include:

```text
descr_data
portfolio
ind_offers
navigation
```

---

## BlockItem

Instance of a Block.

Database table:

```text
block_items
```

A BlockItem has property values and may belong to a category.

---

## BlockItemProperty

Property schema/definition for a Block.

Database table:

```text
block_item_properties
```

Important fields:

```text
key
type
is_collection
is_unique
meta
```

---

## BlockItemPropertyValue

Actual localized value for a BlockItem property.

Database table:

```text
block_item_property_values
```

Important fields:

```text
property_id
item_id
value
value_type
locale
version
```

---

## value_type

Runtime value casting hint used by the backend resolver.

Known examples:

```text
string
integer
float
number
boolean
json
html
```

---

## is_collection

Property-level flag indicating that repeated values should be returned as an array.

---

## version

Reserved or current field for versioning/draft-publish logic.

Do not assume it is unused without checking current data and future plans.

---

# Category / Routing Terms

## BlocksCategories

Laravel model representing a content category/taxonomy node.

Database table:

```text
blocks_categories
```

The pluralized model name is current project reality.

Do not rename casually.

---

## category

Backend content grouping / tree node.

Categories may contain items and may have children.

---

## slug

Frontend/API-facing alias usually derived from backend `key`.

In some API response structures:

```text
slug = category.key
```

---

## key

Backend stable identifier used for blocks, categories, and items.

Often used to find content.

Do not rename or regenerate without migration.

---

## parent_id

Database field linking category to parent category.

Supports hierarchical category tree.

---

## children

Usually means actual nested child categories.

Current status:

```text
partly structural, partly legacy, should be preserved until audited
```

Do not remove from public response unless an explicit migration task says so.

---

## subcategories

Frontend-facing/current API field for child category cards/items in category endpoint responses.

For `/en/blocks/categories/services`, this is important and must be preserved.

---

## childs

Legacy spelling used inside `subcategories` items.

Although grammatically wrong, it may be consumed by frontend code.

Current status:

```text
compatibility key
```

Do not rename to `children` without compatibility layer and separate approved task.

---

# Locale / Scope / Section Terms

## locale

Currently used in backend routes and EAV value filtering.

Example:

```text
/en/blocks/categories/services
```

In backend EAV values:

```text
property_values.locale = en
```

---

## scope

Frontend concept for URL/context prefix.

May currently overlap with locale, but architecturally broader.

Potential future meanings:

```text
language
brand
region
site section
version
```

---

## section

Transitional/backend/frontend term that may overlap with scope or locale depending on context.

Do not globally replace `locale` with `section` or `scope` without a dedicated migration plan.

---

# API Contract Terms

## Contract

Response shape and fields consumed by frontend or other clients.

A contract can be imperfect and still binding.

Example contract fields:

```text
data.content
data.sections
data.subcategories
data.blocks
data.children
```

---

## Response shape

The structural form of API JSON.

Includes keys, nesting, array/object forms, and compatibility names.

Do not change response shape accidentally during refactoring.

---

## Regression reference

Known saved API response used for before/after comparison.

Example:

```text
services.json
```

---

## Compatibility layer

Temporary or permanent mapping that preserves old public keys while allowing improved internal naming.

Example:

```text
internal children → public childs
internal article → public acticle
```

---

# Current Content/Payload Terms

## content

In category endpoint, usually primary page/category content derived from `descr_data` or similar block routing.

Do not confuse with raw DB `content` column or HTML content property without checking context.

---

## sections

API response bucket for block-derived structured page sections.

May be populated through `BlockAttachMap`.

---

## blocks

API response bucket for blocks not mapped to `content` or `sections`, or full block-level output depending on endpoint.

---

## properties

Frontend-facing flattened data object on items or sections.

Often produced from EAV property values.

Example:

```text
properties.hero
properties.benefits
properties.items
```

---

## acticle

Current typo-like key used in some content.

Likely intended target:

```text
article
```

Current status:

```text
compatibility key
```

Do not rename without migration.

---

## items.items

Ambiguous shape where a section key `items` contains an inner `items` array.

Example:

```text
properties.items.items
```

Current status:

```text
naming debt
```

Use aliases in frontend/backend mapping if needed, but do not break existing key without migration.

---

## Rich HTML

HTML content stored in API payload fields.

Frontend should render trusted rich HTML only through controlled components such as `RichText`.

Do not treat user-submitted content as trusted HTML.

---

# Support Class Terms

## EavContentResolver

Support class responsible for transforming EAV item collections into flat API objects/arrays.

Expected role:

```text
pure transformation
```

Should not perform SQL.

Known modes:

```text
single
keyed
array
```

---

## BlockAttachMap

Support class/policy object that maps block keys to response locations.

Examples:

```text
descr_data → content
hero → sections
works → sections keyed
unknown → blocks
```

Current status:

```text
compatibility policy / transitional hardcode
```

Do not migrate to DB metadata casually.

---

# Seeder / Import Terms

## Seeder

Laravel class used to populate database with initial/project content.

In this project, seeders encode real content structure and migration history.

Refactor carefully.

---

## BlockContentHelper

Helper used by seeders to read block JSON content from filesystem.

Important because Laravel 12 local disk behavior differs from older versions.

---

## Content source

JSON file or other data source used to create/update blocks, categories, items, and property values.

---

## Import pipeline

Process of reading content sources and writing normalized records into database tables.

Different from read-side API response pipeline.

---

# Agent Workflow Terms

## `.agents` approach

Project context-engineering approach using structured markdown files for code agents.

Includes:

```text
AGENTS.md
.agents/README.md
.agents/agents.md
.agents/info
.agents/skills
.agents/tasks
```

---

## Skill

Reusable agent instruction module.

Skills should be focused and operational.

They should not duplicate full architecture docs.

---

## Task

Concrete execution prompt for an agent.

A task should define:

```text
- goal
- files
- constraints
- allowed changes
- forbidden changes
- validation
- report format
```

---

## Analysis-first refactor

Preferred workflow for complex changes.

```text
inspect → map → propose → refactor → verify
```

This is the current preferred direction after the narrow backend task cycle was stopped.

---

## Manual regression

Human or agent-executed before/after verification when automated tests are absent or insufficient.

Example:

```text
compare GET /en/blocks/categories/services against services.json
```

---

## Overfitting to task

Failure mode where an agent satisfies a narrow instruction but misses the broader architecture problem.

Avoid by using AIP, contract docs, and analysis-first tasks.

---

# Naming Debt Terms

## Naming debt

Names that are known to be imperfect but are currently embedded in code, data, or contracts.

Examples:

```text
childs
acticle
items.items
locale/scope/section ambiguity
```

Naming debt should be documented first and migrated later.

---

## Legacy

Existing code/data/conventions that may be old or imperfect but still may serve a current function.

Do not delete legacy elements without proving impact.

---

## Secondary context

Context that exists but should not override current `.agents` instructions.

Example:

```text
.gemini/
```

---

# Golden Rules

## For backend refactoring

```text
Preserve public response shape.
Move hidden data loading to the proper layer.
Do not rename compatibility keys casually.
Do not mix read-side refactor with seeder refactor.
```

## For frontend refactoring

```text
View composes.
Index orchestrates.
Presentation receives props.
UI primitives stay generic.
```

## For code agents

```text
Read relevant context.
Inspect source code.
Report uncertainty.
Preserve contracts.
Prefer small justified changes.
Validate before claiming success.
```

