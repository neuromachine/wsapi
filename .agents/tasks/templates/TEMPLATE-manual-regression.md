# TEMPLATE — Manual Regression Checklist

## Status

Task template / checklist.

Use this template when a task cannot rely entirely on automated tests or when a public contract must be manually compared before accepting changes.

---

## Core Principle

Do not overclaim verification.

Syntax checks, static analysis, and runtime endpoint checks are different levels of confidence. Report exactly what was run.

---

## Task / Change Under Review

```text
<TASK-ID or change summary>
```

---

## Regression Target

Define the target to verify:

```text
<endpoint / page / component / seeder / command / payload>
```

Example:

```text
GET /en/blocks/categories/services
Reference payload: services.json
```

---

## Verification Levels

### Level 1 — Static / Syntax

Commands:

```text
<syntax command 1>
<syntax command 2>
```

Example:

```text
php -l app/Repositories/BlockCategoryRepository.php
php -l app/Http/Resources/BlockCategoryResource.php
```

This only proves syntax validity. It does not prove runtime behavior.

---

### Level 2 — Existing Test Suite

Commands:

```text
<test command 1>
<test command 2>
```

Example:

```text
php artisan test
composer test
npm run test:run
npm run build
```

Report whether tests exist and whether they cover the changed behavior.

---

### Level 3 — Runtime / Endpoint Check

Commands or steps:

```text
<curl/browser/Postman command or manual steps>
```

Example:

```text
curl -s <api-host>/en/blocks/categories/services > after.json
```

Verify actual output, not just code shape.

---

### Level 4 — Contract Diff

Compare before/after against a reference payload.

Check:

```text
- top-level envelope
- required keys
- nested keys
- item counts where relevant
- known legacy compatibility fields
- absence of internal implementation leaks
```

---

## Backend Read-Side Checklist

For Laravel API read-side changes:

```text
- response has data envelope
- expected endpoint still resolves
- public keys are preserved
- content field remains compatible
- sections field remains compatible
- subcategories field remains compatible
- blocks field remains compatible
- children field remains compatible if present before
- legacy aliases remain available
- Resource does not perform direct SQL if boundary refactor was intended
- Repository/query layer prepares necessary relations
- EAV internals do not leak into public response
```

---

## Frontend Bridge Checklist

For Vue ↔ Laravel contract changes:

```text
- frontend still reads response.data.data
- route still receives expected shape
- required presentation props remain available
- no backend key was renamed without frontend compatibility
- AppLink/scope behavior remains intact if relevant
- no component now depends on raw EAV internals
```

---

## Seeder / Import Checklist

For seed/import changes:

```text
- seeders run or static safety is documented
- blocks are created/updated as before
- categories are created/updated as before
- items are created/updated as before
- property values remain localized
- content keys remain unchanged
- no locale data is lost
- API output after seeding remains compatible
```

---

## Report Format

The final regression report must state:

```text
1. What was checked.
2. Which commands were run.
3. What passed.
4. What could not be verified.
5. Why it could not be verified.
6. Remaining risk.
```

Do not write:

```text
Regression passed.
```

unless runtime/contract behavior was actually checked.

Prefer precise language:

```text
Syntax checks passed. Runtime endpoint verification was not run because <reason>.
```

---

## Success Criteria

Regression is acceptable if:

```text
- verification level is clearly stated
- protected behavior was checked as much as possible
- uncertainty is explicit
- no unsupported claims are made
```

---

## Failure Criteria

Regression reporting fails if:

```text
- syntax checks are presented as endpoint verification
- static analysis is presented as runtime proof
- missing checks are hidden
- public contract changes are not compared
```
