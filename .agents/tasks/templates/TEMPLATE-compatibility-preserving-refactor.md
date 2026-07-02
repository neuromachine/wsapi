# TEMPLATE — Compatibility-Preserving Refactor

## Status

Task template.

Use this template when an implementation can be improved, but existing consumers depend on current behavior, response shape, names, routes, or data structure.

---

## Core Principle

Improve without breaking.

Compatibility is not optional. If a cleaner design requires a breaking change, document it as a future proposal instead of applying it silently.

---

## Task Name

`<TASK-ID> — <Short compatibility-preserving refactor title>`

---

## Context

Describe the current working behavior.

```text
<Current module and behavior>
```

Describe why the implementation should be improved.

```text
<Problem / debt / complexity>
```

Describe known consumers.

```text
<Frontend route, API consumer, seeder, admin flow, etc.>
```

---

## Reference Contract

The following behavior/shape must remain compatible:

```text
<endpoint / payload / file / interface / component contract>
```

Example:

```text
GET /en/blocks/categories/services
Reference payload: services.json
```

---

## Protected Public Shape

Do not remove or rename these without explicit compatibility support:

```text
<key-1>
<key-2>
<key-3>
```

For API tasks, include full public shape when possible.

Example:

```text
data.content
data.sections
data.subcategories
data.blocks
data.children
```

---

## Compatibility Debt

The following names/structures may be imperfect but must be preserved for now:

```text
<legacy-key-or-behavior-1>
<legacy-key-or-behavior-2>
```

Examples:

```text
childs
acticle
locale vs scope vs section
items naming ambiguity
```

These may be documented as future migration candidates, not renamed in this task.

---

## Allowed Changes

The agent may:

```text
- improve internal implementation
- introduce aliases while preserving old keys
- move logic between internal layers
- reduce duplication
- improve null-safety
- clarify compatibility behavior
- add regression checks
```

---

## Forbidden Changes

Do not:

```text
- break the protected public shape
- remove legacy compatibility keys
- change endpoint URLs
- change route params
- change database schema
- change content data or locale coverage
- change frontend consumers unless explicitly requested
- perform a broad rewrite
```

---

## Required Process

### 1. Identify Current Contract

Before editing, inspect current output/interface and list the fields/behaviors that must remain compatible.

### 2. Identify Internal Debt

Separate internal implementation debt from public contract.

### 3. Refactor Internals

Improve internals while keeping public behavior stable.

### 4. Verify Compatibility

Compare before/after using the best available method.

---

## Regression Checklist

At minimum verify:

```text
- same top-level shape
- same required public keys
- same known legacy aliases
- same route/interface entrypoint
- no unexpected null/missing fields
- no internal IDs/EAV details leaked unless previously public
```

For API tasks, compare reference payload or endpoint output.

---

## Expected Final Report

The final response must include:

```text
1. Protected contract identified.
2. Files changed.
3. Internal changes made.
4. Compatibility preserved.
5. Regression performed.
6. Legacy debt intentionally kept.
7. Future migration notes.
```

---

## Success Criteria

The task succeeds if:

```text
- internal implementation improves
- existing consumers remain compatible
- legacy names remain available if still consumed
- breaking changes are not silently introduced
```

---

## Failure Criteria

The task fails if:

```text
- public shape changes unexpectedly
- the agent renames legacy fields without compatibility
- consumers would need immediate changes
- validation is claimed but not performed
```
