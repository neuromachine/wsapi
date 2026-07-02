# AIP-BE-003 — Resource to Assembler Boundary

## Status

Draft.

## Type

Architecture Improvement Proposal.

This document is not a task file and does not require immediate code changes.

It describes when the project should keep logic inside Laravel Resources and when it should introduce an explicit assembler / payload builder / read-model layer.

---

## Purpose

The current backend read-side system uses Laravel Resources to build frontend-facing JSON from Eloquent models and EAV content.

This is acceptable while Resources remain simple serializers. It becomes risky when Resources start deciding how to query, assemble, route, enrich, sort, or compensate for incomplete model graphs.

The goal of this AIP is to define a boundary:

```text
Resource = serialization boundary
Assembler / PayloadBuilder = response assembly boundary
Repository = data loading boundary
Resolver = EAV transformation boundary
```

---

## Current Tension

The project has a powerful but complex content model:

```text
Category
  → Blocks
    → Items
      → PropertyValues
        → Property
  → Children
    → Items
      → PropertyValues
        → Property
```

The final API response is not a raw database mirror. It is a frontend-oriented payload:

```text
content
sections
subcategories
blocks
children
```

This means someone must assemble the response.

The architectural question is:

```text
Should Resource assemble this directly, or should it serialize a prepared payload/read model?
```

---

## Roles

## Repository / Query Layer

Primary responsibility:

```text
load data
prepare model graph
apply locale filtering
apply eager loading
avoid N+1 queries
return complete input for next layer
```

Repository may know about:

```text
Eloquent relations
whereHas
with
locale filtering
category tree loading
query performance
```

Repository should not become responsible for final JSON naming and frontend aliases unless the project deliberately chooses Repository-as-read-model-builder.

---

## EavContentResolver

Primary responsibility:

```text
transform already-loaded EAV item collections into flat arrays
```

Allowed:

```text
single / keyed / array modes
value_type casting
is_collection handling
local sorting rules if already based on loaded values
```

Not allowed:

```text
SQL queries
category response assembly
frontend route decisions
block attach decisions
```

---

## BlockAttachMap

Primary responsibility:

```text
current compatibility policy for where blocks are attached in response
```

Allowed:

```text
content / sections / blocks routing policy
single/keyed flags
```

Not allowed:

```text
SQL queries
EAV transformation
full response assembly
```

---

## Resource

Primary responsibility:

```text
serialize prepared data into API response
```

Allowed:

```text
explicit field mapping
calling pure helpers on already-loaded data
compatibility key output
simple null-safe transformations
```

Not allowed:

```text
Model::where()
Repository calls
hidden eager loading
business filtering
large response orchestration
recursive query logic
```

---

## Assembler / PayloadBuilder / ReadModel Builder

Potential responsibility:

```text
assemble frontend-facing response zones from prepared data
```

This layer may be introduced when Resource becomes too complex.

Possible names:

```text
BlockCategoryPayloadBuilder
CategoryResponseAssembler
BlockCategoryReadModelBuilder
CategoryPayloadAssembler
```

Naming should follow project conventions and not introduce unnecessary enterprise-style complexity.

---

## When Resource Is Enough

Keep logic in Resource if:

```text
- response shape is simple
- data is already fully loaded
- helper methods are short and pure
- no SQL is performed
- no complex branching exists
- no multiple compatibility modes exist
- no cross-zone orchestration is needed
```

Example acceptable Resource behavior:

```text
return [
  'id' => $this->id,
  'key' => $this->key,
  'content' => $this->resolveContentFromLoadedBlocks(),
];
```

---

## When Resource Is Too Smart

Resource is too smart when it:

```text
- performs DB queries
- loads missing relations
- decides how to construct several response zones
- mixes category tree assembly with block assembly
- contains long private methods with business meaning
- contains sorting/filtering policy that belongs elsewhere
- compensates for Repository not loading enough data
- becomes hard to test without full Laravel request context
```

Warning smell:

```text
Resource knows too much about how to obtain data, not only how to present it.
```

---

## When to Introduce an Assembler

Introduce an assembler/read-model builder if at least several of these are true:

```text
- category response has multiple zones: content, sections, subcategories, blocks, children
- each zone has different assembly rules
- Resources contain repeated transformation logic
- compatibility aliases must be maintained
- endpoint-specific response variants are emerging
- future Server-Driven UI payloads are planned
- testing response assembly separately would be valuable
```

For the current project, `BlockCategoryResource` is a candidate for eventual extraction if future work expands beyond a minimal boundary cleanup.

---

## What an Assembler Should Do

A good assembler may:

```text
- accept prepared Eloquent model/read graph
- call EavContentResolver for loaded item collections
- apply BlockAttachMap policy
- compose content / sections / subcategories / blocks / children
- preserve compatibility keys
- return plain PHP arrays or lightweight DTO/read model
```

It should not:

```text
- perform heavy SQL unless explicitly designed as query+builder
- become a second Repository accidentally
- become a dumping ground for unrelated helpers
- hide breaking response changes
```

---

## Possible Pipeline With Assembler

```text
Controller
  → BlockCategoryRepository
    → prepared BlocksCategories model graph
      → BlockCategoryPayloadBuilder
        → array/read model with content, sections, subcategories, blocks, children
          → BlockCategoryResource
            → final Laravel Resource response
```

Alternative:

```text
Controller
  → BlockCategoryQuery
    → BlockCategoryReadModelBuilder
      → BlockCategoryReadModel
        → Resource
```

Do not introduce all of this at once unless the complexity requires it.

---

## Minimal Transitional Design

A practical near-term compromise:

```text
Repository prepares complete graph.
Resource keeps current public response keys.
Private Resource helpers are made pure and query-free.
If helpers continue growing, extract them into PayloadBuilder.
```

This staged approach avoids premature abstraction while still moving toward cleaner boundaries.

---

## Assembler Naming Guidance

Preferred simple names:

```text
BlockCategoryPayloadBuilder
BlockCategoryResponseAssembler
```

Avoid overly abstract names such as:

```text
UniversalContentHydrationOrchestrator
DynamicEntityProjectionManager
```

The project should favor practical readability over architectural theater.

---

## Inputs and Outputs

A good builder should have an explicit input/output idea.

Input:

```text
BlocksCategories $category
string $locale or context object
```

Output:

```text
array{
  content: array,
  sections: array,
  subcategories: array,
  blocks: array,
  children: array,
}
```

Or a lightweight read model if PHP type discipline is later desired.

---

## Compatibility Layer

Assembler can be a good place for compatibility aliases:

```text
childs remains for frontend compatibility
children can be added later as alias
acticle can be preserved while article alias is introduced later
section can remain while scope migration is planned
```

But compatibility must be explicit, not accidental.

---

## Testing / Regression Benefit

If response assembly is extracted, it becomes easier to check:

```text
- services endpoint payload shape
- subcategories item fields
- block attach behavior
- EAV flattening output
- compatibility aliases
```

Even without full feature tests, a clear builder boundary makes manual regression easier.

---

## Risks of Introducing Assembler Too Early

Assembler is harmful if it:

```text
- duplicates Repository responsibilities
- duplicates Resource responsibilities
- becomes another large procedural file
- is introduced before current behavior is understood
- changes public response shape silently
- makes debugging harder
```

Therefore the decision should be based on observed complexity, not aesthetic preference.

---

## Recommended Decision Rule

Use this rule for future tasks:

```text
If the task only removes SQL from Resource and improves eager loading,
keep Repository + Resource.

If the task reorganizes content/sections/subcategories/blocks/children assembly,
consider BlockCategoryPayloadBuilder.

If the task introduces new endpoint shapes or SDUI page configs,
strongly consider read-model / assembler layer.
```

---

## Relation to TASK-BE-002

A future complex read-side refactor may choose to introduce an assembler, but it should not be forced.

The task should require the agent to:

```text
1. inspect current complexity
2. choose smallest safe design
3. justify whether assembler is needed
4. preserve response contract
5. report remaining debt
```

---

## Summary

Resource should not be made smarter to support complex response assembly.

The project has three reasonable stages:

```text
1. Clean Resource boundary.
2. Keep pure helper methods if complexity remains moderate.
3. Extract BlockCategoryPayloadBuilder if response assembly continues growing.
```

The guiding principle:

```text
Do not introduce architecture because it sounds clean.
Introduce it when it removes real confusion without breaking the contract.
```
