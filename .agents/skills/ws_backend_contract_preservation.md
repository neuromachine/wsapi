# Skill: WS Backend Contract Preservation

## Purpose

Use this skill whenever a backend change may affect frontend-visible JSON.

In the WebSolutions project, API response shape is part of the product architecture. Refactoring is allowed, but accidental contract drift is not.

Core principle:

```text
Public contract first, internal cleanup second.
```

---

## When to use

Use for any change involving:

```text
- Laravel Resources
- category endpoints
- item endpoints
- block endpoints
- EAV response mapping
- field naming
- API envelope structure
- frontend-consumed payloads
```

Primary reference endpoint:

```text
GET /en/blocks/categories/services
```

Reference payload:

```text
services.json
```

---

## Required context

Read these files before changing public response structures:

```text
.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md
.agents/info/FE-BACKEND-CONTRACT-BRIDGE.md
.agents/info/improvements/AIP-BE-002-category-response-contract.md
.agents/info/improvements/AIP-BE-005-naming-and-compatibility-layer.md
```

---

## Contract layers

### 1. Laravel Resource envelope

The frontend expects useful data inside:

```text
response.data.data
```

Do not remove the Laravel Resource envelope.

### 2. Category endpoint root shape

For category endpoints, preserve known root fields unless a task explicitly allows migration:

```text
data.id
data.key
data.name
data.description
data.content
data.parent_id
data.created_at
data.updated_at
data.section
data.sections
data.subcategories
data.blocks
data.children
```

### 3. Services category shape

For `/en/blocks/categories/services`, preserve the current meaning of:

```text
content
subcategories
sections
blocks
children
```

### 4. Subcategory item shape

Preserve known subcategory fields:

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

`childs` is a legacy public field. Do not rename it to `children` without a compatibility layer and explicit approval.

---

## Refactor-safe behavior

A backend refactor may change:

```text
- how data is queried
- where response assembly happens
- private method names
- eager loading strategy
- internal class boundaries
```

A backend refactor must not accidentally change:

```text
- public JSON key names
- nesting structure
- field meaning
- Laravel envelope
- locale-specific value selection
- EAV flat object behavior
```

---

## Compatibility aliases

If internal naming is improved, expose compatibility aliases until frontend migration is explicitly approved.

Examples:

```text
internal: children
public:   childs

internal: article
public:   acticle

internal: scope/section context
public:   locale route segment, if currently used
```

Never remove the old public key in the same task that introduces the new one unless the task explicitly states this migration is allowed.

---

## Manual contract check

For changes around category responses, compare before/after against the reference payload.

Minimum checklist:

```text
- response has top-level data
- data.content exists
- data.subcategories exists
- data.subcategories is an array
- subcategory items retain id / slug / childs
- EAV fields like title / descr / content / metadata / priority are still present when expected
- data.blocks exists
- data.sections exists
- data.children remains compatible
- no EAV internal IDs leak unexpectedly
```

If runtime verification cannot be executed, report that clearly and provide a static compatibility analysis.

Do not claim runtime verification if only syntax checks were run.

---

## Reporting requirements

The final report must explicitly say:

```text
- which public fields were preserved
- whether the endpoint was actually executed
- whether comparison was runtime or static
- any fields whose usage is uncertain
- any compatibility debt left for future migration
```

---

## Failure cases

A refactor fails this skill if:

```text
- public keys are renamed without aliasing
- `subcategories` disappears or changes meaning
- `childs` is removed because it looked wrong
- `response.data.data` assumption is broken
- runtime regression is claimed without running it
- frontend-facing fields are treated as internal cleanup targets
```

