# Skill: WS Backend Assembler Decision

## Purpose

Use this skill when a backend refactor touches response assembly, category payload construction, or the boundary between Repository, Resource, Resolver, and possible PayloadBuilder / Assembler classes.

The goal is not to introduce a new layer automatically. The goal is to decide whether the existing Repository + Resource + Support helpers remain enough, or whether a small named assembler/read-model builder would make the system clearer.

---

## Core Rule

Do not create an Assembler just because the code looks complex.

Create or propose an Assembler only when it reduces hidden behavior, clarifies boundaries, and preserves the public API contract.

```text
Prefer clarity over architecture ceremony.
Prefer compatibility over purity.
Prefer small named collaborators over a new God object.
```

---

## Current Project Context

The backend read-side flow currently involves:

```text
Controller
  → Repository
    → Eloquent model graph
      → Resource
        → EavContentResolver
        → BlockAttachMap
          → JSON response
```

Known tension points:

```text
- Resource can become too smart.
- Repository can become too large.
- EAV transformation should stay pure.
- BlockAttachMap is current compatibility policy.
- Category endpoint response shape is public contract.
```

The central reference endpoint is:

```text
GET /en/blocks/categories/services
```

Its current response shape must remain compatible.

---

## When Repository + Resource Is Enough

Do not introduce an assembler when the change can be safely expressed as:

```text
- better eager loading in Repository
- simpler Resource methods
- no SQL in Resource
- no response shape change
- no duplicated response assembly
```

This is usually enough when:

```text
- only one endpoint is affected
- only one Resource contains the issue
- logic is easy to read after moving queries out of Resource
- EavContentResolver and BlockAttachMap remain sufficient
```

---

## When to Consider an Assembler / PayloadBuilder

Consider a small assembler if several of these are true:

```text
- Resource contains multiple large methods that assemble domain-specific payloads
- response assembly requires combining content / sections / subcategories / blocks / children
- the same assembly logic is duplicated across Resources
- Repository would become a God object if it owned all response shaping
- the endpoint has a stable public contract that deserves a named compatibility layer
- future changes will need aliasing, compatibility, or gradual migration
```

Possible names:

```text
App\Support\Blocks\BlockCategoryPayloadBuilder
App\Support\Blocks\CategoryResponseAssembler
App\Services\Blocks\BlockCategoryReadModelBuilder
```

Use project naming conventions if they already exist.

---

## What an Assembler May Do

A small assembler may:

```text
- receive an already prepared category model/read graph
- assemble content / sections / subcategories / blocks / children
- call EavContentResolver on already loaded collections
- apply compatibility aliases such as childs
- preserve legacy public keys
- keep Resource thin and explicit
```

---

## What an Assembler Must Not Do

An assembler must not become a hidden Repository.

Avoid:

```text
- direct Model::where() calls unless explicitly designed as a query builder
- hidden eager loading
- route-level decisions
- frontend-specific rendering logic
- DB schema migration logic
- generic mega-service behavior
```

If an assembler needs SQL, reconsider the design. That logic probably belongs in Repository / query layer.

---

## Recommended Decision Process

Before introducing a new class, answer:

```text
1. What exact confusion does this class remove?
2. Which current method becomes simpler?
3. Which public contract does it preserve?
4. Does it avoid turning Repository into a God object?
5. Does it avoid turning Resource into a query/assembly layer?
6. Can this be done with a smaller private method instead?
7. Will future agents understand this class from its name?
```

If the answers are weak, do not introduce the class.

---

## Safe Refactor Pattern

A safe pattern:

```text
Repository
  prepares full model graph

PayloadBuilder / Assembler
  assembles endpoint-specific public shape from prepared graph

Resource
  returns explicit serialized array
```

Example responsibility split:

```text
BlockCategoryRepository
  load category, blocks, items, children, property values

BlockCategoryPayloadBuilder
  build content, sections, subcategories, blocks, children arrays

BlockCategoryResource
  expose final array and envelope compatibility
```

This is only a direction. Do not force it if the current task can be done more simply.

---

## Compatibility Warning

Do not use an assembler introduction as an excuse to rename public keys.

Preserve:

```text
content
sections
subcategories
blocks
children
childs
```

Naming cleanup belongs to a separate compatibility/migration task.

---

## Final Report Requirements

If an assembler is introduced or proposed, report:

```text
- why the existing Resource/Repository split was not enough
- why this class name and location were chosen
- what logic moved into it
- what logic did not move into it
- how response compatibility was preserved
- what future cleanup remains
```

If no assembler is introduced, report why the simpler approach was sufficient.
