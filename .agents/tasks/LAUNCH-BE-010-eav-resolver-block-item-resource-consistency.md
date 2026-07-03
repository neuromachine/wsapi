# LAUNCH — TASK-BE-010 EAV Resolver / BlockItemResource Consistency

Execute:

```text
.agents/tasks/TASK-BE-010-eav-resolver-block-item-resource-consistency.md
```

## Mode

Practical code refactor.

## Goal

Reduce duplicated or divergent EAV flatten/cast logic between:

```text
app/Support/EavContentResolver.php
app/Http/Resources/BlockItemResource.php
```

Preserve API response shape.
Do not change frontend.
Do not change DB schema.
Do not introduce a large service layer.

Expected report:

```text
.agents/reports/REPORT-BE-010-eav-resolver-block-item-resource-consistency.md
```

