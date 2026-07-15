# Skill: WS Agent Regression Protocol

## Purpose

Use this skill whenever a code-agent performs a refactor or architectural cleanup in this project.

The goal is to prove that the change improved the system without breaking existing behavior.

---

## Core Rule

A refactor is not complete when the code looks cleaner.

A refactor is complete when compatibility has been checked and the result can be reviewed safely.

```text
Improve without breaking.
Verify before claiming success.
Report uncertainty honestly.
```

---

## Regression Levels

Use the strongest available level.

### Level 1 — Syntax / Static Safety

Examples:

```bash
php -l app/Http/Resources/BlockCategoryResource.php
php -l app/Repositories/BlockCategoryRepository.php
```

This only proves syntax, not behavior.

Do not describe syntax-only validation as endpoint validation.

---

### Level 2 — Existing Test Commands

Use if available and relevant:

```bash
php artisan test
composer test
vendor/bin/pint --test
npm run test:run
npm run build
```

Inspect project scripts before inventing commands.

---

### Level 3 — Endpoint Runtime Check

For backend API refactor, prefer checking a real endpoint.

Primary reference endpoint:

```text
GET /en/blocks/categories/services
```

If the app can run locally, compare before/after response shape.

---

### Level 4 — Contract Diff

Compare against reference payloads such as:

```text
services.json
respond_visarun_system.json
```

Check keys and structural compatibility rather than exact timestamps/ordering unless ordering is part of the contract.

---

## Minimum Manual Regression for Backend Read-Side

For category endpoint changes, verify:

```text
- response has data envelope
- data.content exists
- data.sections exists
- data.subcategories exists
- data.blocks exists
- data.children exists or remains compatible
- subcategories contain id, slug, childs
- subcategories retain EAV fields like title, descr, content, metadata, priority
- no EAV internals leak
- no direct SQL remains in Resource if the task targeted Resource boundary
```

---

## Minimum Manual Regression for Frontend Bridge

For frontend-impacting changes, verify:

```text
- /en/services can still consume category endpoint data
- response.data.data remains the frontend payload root
- presentation components do not need EAV knowledge
- legacy keys consumed by frontend are preserved
```

If frontend runtime cannot be started, state this and provide static evidence.

---

## Minimum Manual Regression for Seeders

For seed/import changes, verify:

```text
- seed command runs or exact blocker is reported
- content keys remain available
- locale data remains present
- no content text was changed unless requested
- /en/blocks/categories/services still returns expected data after seed/import
```

---

## Report What Was Not Verified

Always distinguish:

```text
- checked by command
- checked by static inspection
- assumed from code structure
- not checked
```

Bad report:

```text
Response shape preserved: yes.
```

Better report:

```text
Response shape appears preserved by static inspection, but runtime endpoint was not executed because the local server was not running.
```

---

## Do Not Overclaim

Never claim runtime success if only syntax checks ran.

Never claim frontend compatibility if no endpoint or component check was performed.

Never claim full regression if only one endpoint was checked.

---

## Suggested Final Report Format

Use this structure:

```text
1. Files inspected
2. Files changed
3. Structural improvement
4. Public contract impact
5. Validation run
6. Manual regression notes
7. What was not verified
8. Remaining risks
9. Recommended next step
```

---

## Review Checklist Before Final Answer

Before reporting completion, confirm:

```text
- task scope was respected
- no unrelated broad rewrite happened
- public response keys were preserved
- compatibility debts were not silently renamed
- validation commands are accurately described
- uncertainty is explicit
```

---

## Core Reminder

Agent output must be reviewable.

A smaller verified refactor is better than a larger unproven one.
