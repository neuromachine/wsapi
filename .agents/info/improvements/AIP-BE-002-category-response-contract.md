# AIP-BE-002 — Category Response Contract

## Status

Draft.

## Type

Architecture Improvement Proposal.

This document is not a task file and is not an instruction to refactor code immediately.

It defines how the current category endpoint response should be understood, protected, and gradually improved.

---

## Purpose

The backend category endpoint is one of the central read-side contracts between Laravel API and Vue SPA.

The current response shape has grown organically and contains both intentional structures and compatibility debt. Before broad backend refactoring, this response must be treated as a contract.

The goal of this AIP is to separate:

```text
what is public contract
what is legacy compatibility
what is internal implementation detail
what may be improved only through a migration path
```

---

## Reference Endpoint

Primary reference endpoint:

```text
GET /en/blocks/categories/services
```

Frontend route depending on it:

```text
/en/services
```

Current request chain:

```text
Vue route /en/services
  → frontend page lifecycle
    → API request /en/blocks/categories/services
      → BlockCategoryController
        → BlockCategoryRepository
          → BlockCategoryResource
            → JSON Resource response
```

Reference payload:

```text
services.json
```

---

## Current Public Shape

The current response is wrapped by Laravel Resource envelope:

```text
response.data.data
```

The effective payload root is:

```text
data
```

Current root-level fields expected to remain compatible:

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

These fields may not all be equally clean or equally future-proof, but they form the current API surface.

---

## Contract Zones

The category response should be understood as several zones.

```text
category identity
category content
section blocks
subcategories
blocks fallback / raw blocks
recursive children / structure
compatibility metadata
```

---

## Zone 1 — Category Identity

Fields:

```text
id
key
name
description
parent_id
created_at
updated_at
section
```

Current meaning:

```text
id          database id of category
key         stable category slug/key
name        category display/system name
description optional category description
parent_id   parent category id or null
section     currently mirrors request locale / section context
```

### Contract status

Stable for current frontend consumption.

### Risks

`section` is semantically overloaded. It currently behaves close to `locale`, while frontend architecture increasingly thinks in terms of `scope`.

### Rule

Do not rename `section` to `locale`, `scope`, or anything else in a refactor task unless a compatibility/migration layer is explicitly introduced.

---

## Zone 2 — `content`

Current role:

```text
content = main descriptive EAV payload for the current category
```

For `/en/blocks/categories/services`, `content` contains page-level descriptive fields such as:

```text
title
descr
content
metadata
priority
```

### Contract status

High priority compatibility field.

The frontend may render page intro, SEO/meta content, descriptive HTML, and page title-related data from this object.

### Rule

Do not remove or rename `data.content`.

Do not move it into `sections` or `blocks` without a compatibility alias.

---

## Zone 3 — `sections`

Current role:

```text
sections = named section payloads derived from blocks attached to the category
```

This is populated through current block routing policy, especially `BlockAttachMap`.

For `/services`, this may currently be empty, but empty does not mean unused globally.

### Contract status

Stable structural field.

### Rule

Always preserve the field.

An empty array/object should stay compatible with current frontend expectations.

Do not assume that `sections: []` on one endpoint means the concept can be removed.

---

## Zone 4 — `subcategories`

Current role:

```text
subcategories = frontend-facing list of direct child categories enriched with EAV descriptive content
```

For `/services`, this is a primary payload zone.

Typical item fields:

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

### Contract status

Critical for `/en/services`.

### Rule

Do not remove `subcategories`.

Do not reduce it to raw category records.

Do not remove EAV-derived fields from each item.

Do not rename `slug` to `key` in this response without compatibility alias.

---

## Zone 5 — `childs`

Current role:

```text
childs = legacy nested child-list field inside subcategories items
```

The spelling is not ideal, but it is currently part of frontend-facing data.

### Contract status

Legacy compatibility field.

### Rule

Preserve it until a frontend migration removes or aliases it.

Prefer:

```text
keep childs
optionally add children_alias only in a planned migration
```

Avoid:

```text
remove childs
rename childs → children directly
```

---

## Zone 6 — `blocks`

Current role:

```text
blocks = fallback or block-list representation of category-attached blocks
```

This zone may contain raw-ish block resources or transformed block payloads depending on current Resource logic.

### Contract status

Preserve unless exact frontend usage is audited.

### Rule

Do not remove `blocks` in a read-side refactor.

Do not assume `blocks` is redundant just because `content` and `sections` exist.

---

## Zone 7 — `children`

Current role:

```text
children = structural/category-tree field
```

This field may overlap conceptually with `subcategories`, but they should not be treated as equivalent without audit.

### Contract status

Unclear but preserved.

### Rule

Do not remove `children` in a compatibility-preserving refactor.

Do not collapse `children` and `subcategories` into one field until frontend usage and target contract are explicitly redesigned.

---

## Public Contract vs Internal Implementation

### Public contract

```text
response shape
field names
field existence
field meaning as consumed by Vue
localized content availability
```

### Internal implementation

```text
Repository eager loading
Resource helper methods
EavContentResolver internals
BlockAttachMap strategy
assembler/read-model class names
private method names
```

The implementation may change. The public contract should remain stable unless a migration plan is approved.

---

## Known Compatibility Debt

```text
childs vs children
section vs locale/scope
content.content naming
subcategories vs children overlap
blocks vs sections overlap
priority sorting in Resource/Resolver
implicit shape through attributesToArray
```

These should be documented and improved through staged migration, not fixed by accidental breaking changes.

---

## Compatibility Strategy

When improving this endpoint:

```text
1. Preserve current keys.
2. Improve internal data loading first.
3. Add aliases only if explicitly approved.
4. Deprecate old names only after frontend migration.
5. Keep reference payloads for regression.
6. Avoid schema changes unless a separate AIP/task approves them.
```

---

## Recommended Future Contract Direction

A cleaner future response may eventually separate:

```text
page
structure
sections
children
blocks
meta
```

Potential future shape:

```json
{
  "data": {
    "id": 2,
    "key": "services",
    "page": {},
    "sections": {},
    "children": [],
    "blocks": [],
    "meta": {}
  }
}
```

But this is not the current contract.

Current compatibility wins over theoretical cleanliness.

---

## What Future Refactor Tasks Must Say

Any future task touching category response must explicitly state:

```text
- whether JSON shape must remain identical
- whether aliases are allowed
- whether fields may be removed
- whether frontend changes are included
- whether DB schema changes are allowed
- which endpoint is the regression reference
```

If this is not stated, default to:

```text
preserve current shape
```

---

## Acceptance Criteria for Contract-Safe Refactor

A contract-safe backend refactor is acceptable if:

```text
- GET /en/blocks/categories/services still returns response.data.data
- root keys remain compatible
- content remains available
- subcategories remain available
- subcategories items retain legacy keys
- blocks remains available
- sections remains available
- children remains available or is compatibly preserved
- no frontend-facing EAV fields disappear unexpectedly
```

---

## Warning Signs

A refactor is unsafe if it:

```text
- renames childs → children without alias
- removes subcategories because children exists
- removes blocks because sections exists
- changes content from object to array/string
- moves page content under a new key only
- drops metadata or priority from subcategory items
- changes locale/section semantics silently
- treats services.json as obsolete without replacement
```

---

## Summary

The category response contract is imperfect but active.

The correct strategy is:

```text
preserve current shape
improve internal implementation
document debt
migrate names and structure later through explicit compatibility plan
```
