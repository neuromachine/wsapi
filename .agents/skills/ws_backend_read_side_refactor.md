# Skill: WS Backend Read-Side Refactor

## Purpose

Use this skill when working on the Laravel backend read-side flow for Blocks, Categories, Items, EAV content, and API response assembly.

This skill is not a task. It does not tell the agent to edit code by itself. It defines the preferred operating mode for backend read-side refactoring in the WebSolutions project.

Core principle:

```text
Improve without breaking.
```

The goal is to improve the existing system under real project constraints, not to replace it with a theoretically perfect architecture.

---

## When to use

Use this skill for work involving files such as:

```text
app/Http/Controllers/Api/BlockCategoryController.php
app/Repositories/BlockCategoryRepository.php
app/Repositories/BlockRepository.php
app/Repositories/BlockItemRepository.php
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

Primary reference endpoint:

```text
GET /en/blocks/categories/services
```

---

## Required context

Before editing, read the relevant project context:

```text
.agents/info/BE-00-overview.md
.agents/info/BE-02-blocks-read-side-flow.md
.agents/info/BE-03-eav-domain.md
.agents/info/BE-04-repository-layer.md
.agents/info/BE-05-resource-layer.md
.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md
.agents/info/BE-RESOURCE-BOUNDARY.md
.agents/info/improvements/AIP-BE-001-read-side-refactor.md
.agents/info/improvements/AIP-BE-002-category-response-contract.md
.agents/info/improvements/AIP-BE-003-resource-to-assembler-boundary.md
```

Do not treat AIP files as direct implementation instructions. They describe possible directions and trade-offs.

---

## Operating protocol

Follow this sequence:

```text
inspect → map → propose → refactor → verify → report
```

### 1. Inspect

Read the current code before choosing an implementation.

Identify:

```text
- route and controller flow
- repository query logic
- model relations
- resource serialization logic
- EAV transformation logic
- response shape consumed by frontend
- hidden SQL in serialization layers
- legacy compatibility fields
```

### 2. Map

Create a mental map of the current flow:

```text
HTTP route
  → Controller
    → Repository / query layer
      → Eloquent model graph
        → Resource / resolver / attach map
          → JSON response
```

Do not assume that a field is unused because it looks redundant or poorly named.

### 3. Propose

Choose the smallest safe refactor that improves responsibility boundaries.

Allowed directions:

```text
- improve repository loading
- clean Resource responsibilities
- introduce a small read-side assembler only if clearly justified
- reduce duplicated EAV transformation
- preserve compatibility aliases
```

### 4. Refactor

Change only the files required by the selected direction.

Do not rewrite unrelated backend layers.

### 5. Verify

Validate the public response shape where possible.

At minimum, reason against the current reference endpoint:

```text
GET /en/blocks/categories/services
```

### 6. Report

Report what changed, why, what was preserved, and what remains risky.

---

## Design direction

The backend read-side should move toward this responsibility model:

```text
Controller
  Receives request and returns response.

Repository / query layer
  Prepares the data graph.

Optional assembler / payload builder
  Coordinates complex response assembly when Resource becomes too smart.

Resource
  Serializes prepared data.

EavContentResolver
  Converts already-loaded EAV items into flat objects.

BlockAttachMap
  Applies current compatibility routing policy for block output.
```

This is a direction, not a demand to introduce every layer immediately.

---

## Acceptable changes

The agent may:

```text
- move DB access out of Resources
- improve eager loading
- make response assembly more explicit
- add small named helpers if they reduce complexity
- preserve legacy keys through compatibility layers
- clarify method names and private helper structure
- reduce duplicated transformation code
- add manual regression instructions
```

---

## Forbidden changes

Do not:

```text
- change database schema
- change routes
- rename public JSON keys without compatibility
- remove subcategories, children, childs, content, sections, or blocks
- change frontend code during backend read-side refactor
- rewrite the whole module
- introduce broad abstractions without proof of need
- migrate BlockAttachMap to DB metadata in the same task
- change seed content in a read-side task
```

---

## Compatibility reminder

The current system contains legacy names and transitional structures. Preserve them until a separate compatibility/migration task is approved.

Examples:

```text
subcategories
children
childs
locale
section
scope
acticle
items
```

Bad naming is not, by itself, permission to break the API.

---

## Success criteria

A good read-side refactor should result in:

```text
- less hidden behavior
- clearer layer responsibility
- no broken frontend contract
- less SQL or query logic inside Resources
- easier future extension of category/page payloads
- explicit notes about remaining architectural debt
```

