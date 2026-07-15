# LAUNCH — TASK-BE-008 Explicit Resource Serialization Hardening

Execute:

```text
.agents/tasks/TASK-BE-008-explicit-resource-serialization-hardening.md
```

## Mode

Practical code refactor.

Do not create new methodology.
Do not stop because BE-005 endpoint tests are failing.
Do not refactor unrelated files.

## Goal

Make `BlockCategoryResource` explicit and remove broad model attribute leakage while preserving the current API response contract.

## Important

Keep these public fields intact:

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

Keep legacy subcategory key:

```text
childs
```

## Expected changed file

Main expected file:

```text
app/Http/Resources/BlockCategoryResource.php
```

Report:

```text
.agents/reports/REPORT-BE-008-explicit-resource-serialization-hardening.md
```

