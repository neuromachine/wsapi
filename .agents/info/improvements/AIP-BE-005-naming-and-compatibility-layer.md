# AIP-BE-005 — Naming and Compatibility Layer

## Status

Draft.

## Type

Architecture Improvement Proposal.

This document is not a direct code task. It defines the problem space around naming debt and public compatibility in the WS backend/frontend API contract.

---

## 1. Purpose

The purpose of this AIP is to describe how the project should treat inconsistent or legacy names without breaking the working frontend and API consumers.

The project currently contains several terms and keys that are imperfect but already part of the working system. The goal is to create a safe approach to naming cleanup.

The core rule:

```text
Do not rename public contract fields just because the new name is better.
Introduce compatibility first, migrate usage second, remove legacy only later.
```

---

## 2. Current Naming Debt

Known examples:

```text
childs vs children
subcategories vs children
acticle vs article
items as section payload vs items as EAV item collection
locale vs section vs scope
content as HTML field vs content as API response node
blocks as DB table vs blocks as response fallback bucket
```

Some of these are spelling issues. Some are architectural ambiguity. Some are transitional terms caused by the frontend/backend evolving at different speeds.

---

## 3. Why This Matters

Naming debt becomes dangerous when code-agents or developers assume that an ugly name is unused or safe to correct.

For example:

```text
childs
```

is not good English, but it may be consumed by frontend components. Renaming it to `children` in the API response would be a breaking change unless the frontend is migrated or both keys are served during a compatibility window.

Similarly:

```text
acticle
```

is likely a typo for `article`, but if it exists in seeded data or frontend mapping, direct renaming may break a page.

---

## 4. Public Contract vs Internal Naming

The project must distinguish between:

```text
Public API contract
Internal implementation detail
Legacy compatibility alias
Future preferred name
```

A public contract key is any field that the frontend may consume from API response.

An internal implementation name is a class, private method, variable, or local helper that can be renamed if behavior remains the same.

A compatibility alias is an old public key preserved while a better name is introduced.

A future preferred name is the target naming after migration.

---

## 5. Known Terms and Recommended Handling

### 5.1 `childs`

Current role:

```text
Legacy field inside subcategory item payload.
Likely consumed by frontend services components.
```

Preferred future:

```text
children
```

Recommended strategy:

```text
Phase 1: keep childs
Phase 2: add children alias if needed
Phase 3: migrate frontend to children
Phase 4: remove childs only after audit
```

Do not remove `childs` in backend refactor tasks.

---

### 5.2 `children`

Current role:

```text
Root-level or structural recursive category relation/response node.
```

Potential ambiguity:

```text
May represent raw recursive category tree rather than UI-ready subcategories.
```

Recommended strategy:

```text
Preserve root children until audited.
Do not merge it blindly with subcategories.
```

---

### 5.3 `subcategories`

Current role:

```text
Frontend-facing category list enriched with EAV fields.
For /services it appears to represent service directions/categories.
```

Recommended strategy:

```text
Treat as current public contract.
Keep name until frontend/backend contract migration is explicitly planned.
```

---

### 5.4 `acticle`

Current role:

```text
Likely typo/legacy content key for article-like HTML/text content.
```

Preferred future:

```text
article
```

Recommended strategy:

```text
Do not rename seed/property key directly.
Introduce backend or frontend alias article → acticle only after auditing all consumers.
Preserve acticle in current payloads until migration.
```

---

### 5.5 `items`

Ambiguity:

```text
EAV BlockItems collection
pricing/package items
section items
JSON property named items
```

Recommended strategy:

```text
In documentation, qualify the term:
- BlockItem for DB entity
- section.items for section payload
- packages alias for compred pricing-like content
- items property when referring to raw current API shape
```

Do not rename `items` globally.

---

### 5.6 `locale`, `scope`, `section`

Current state:

```text
Backend route uses {locale}.
Frontend increasingly uses scope as URL/context prefix.
Section is a possible future generalized context name.
```

Recommended strategy:

```text
Do not mechanically rename locale → scope.
Document current dual role.
Introduce scope/section only through explicit architecture migration.
```

---

## 6. Compatibility Layer Concept

A compatibility layer allows old and new names to coexist during migration.

Example:

```php
return [
    'childs' => $childrenPayload,   // legacy public key
    'children' => $childrenPayload, // future alias, if approved
];
```

However, aliases should not be added casually. Each alias increases payload size and ambiguity.

Alias introduction should have:

```text
- reason
- owner
- migration target
- deprecation plan
- frontend usage check
```

---

## 7. Agent Rules for Naming

Code-agents must not:

```text
- fix typos in public JSON keys without explicit approval
- rename childs to children automatically
- rename acticle to article automatically
- rename locale to scope automatically
- assume ugly names are unused
- remove compatibility fields during refactor
```

Code-agents may:

```text
- document naming debt
- improve internal variable names
- add comments around legacy fields
- propose compatibility aliases
- suggest migration plan
```

---

## 8. Suggested Migration Workflow

### Stage 1 — Audit

Find all producers and consumers of the name.

### Stage 2 — Classify

Determine whether the name is:

```text
internal only
public contract
legacy compatibility
unused
unknown
```

### Stage 3 — Introduce Alias

Only if needed and approved.

### Stage 4 — Migrate Consumers

Update frontend/backend consumers to preferred name.

### Stage 5 — Deprecate Legacy

Only after usage is removed and regression confirms compatibility.

---

## 9. Relation to AIP-BE-002

`AIP-BE-002-category-response-contract.md` defines the current category response contract.

This AIP defines how contract names can evolve safely.

Together they protect the project from accidental breaking changes during refactor.

---

## 10. Success Criteria

This improvement direction succeeds if:

```text
- naming debt is documented
- public contract keys are preserved during refactor
- future preferred names are clear
- compatibility aliases are deliberate, not accidental
- frontend migration can happen gradually
```

---

## 11. Failure Criteria

This direction fails if:

```text
- fields are renamed without consumer audit
- frontend breaks due to cleanup
- old and new names coexist forever without plan
- documentation hides real compatibility risk
- code-agents treat naming cleanup as harmless
```

---

## 12. Current Recommendation

For the next backend refactor tasks:

```text
Preserve current public names.
Improve internal structure first.
Treat naming cleanup as a separate migration topic.
```

Do not combine naming cleanup with read-side architecture refactor unless explicitly approved.
