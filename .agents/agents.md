# agents.md — Machine Bootstrap for WebSolutions Backend Runs

## Mode

Use this file as the machine-readable bootstrap for agentic coding runs in the WebSolutions repository.
Do not treat it as a task by itself. Always execute a specific task file from `.agents/tasks/`.

## Active task for this package

```text
.agents/tasks/TASK-BE-001-block-category-resource-boundary-refactor.md
```

## Read order for TASK-BE-001

Before editing code, read in this order:

```text
1. AGENTS.md
2. .agents/agents.md
3. .agents/tasks/TASK-BE-001-block-category-resource-boundary-refactor.md
4. .agents/info/BE-00-overview.md
5. .agents/info/BE-03-eav-domain.md
6. .agents/info/BE-04-repository-layer.md
7. .agents/info/BE-05-resource-layer.md
8. .agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md
9. .agents/info/BE-RESOURCE-BOUNDARY.md
10. .agents/skills/ws_backend_resource_boundary.md
11. .agents/skills/ws_backend_category_contract.md
12. .agents/skills/ws_backend_repository_loading.md
13. .agents/skills/ws_backend_block_category_guardrails.md
14. .agents/skills/ws_backend_manual_regression.md
```

Then inspect the source files listed by the task.

## Legacy context rule

`.gemini/` is secondary / legacy context. If `.gemini/` conflicts with `.agents/*`, follow `.agents/*` for this run.

## Current backend objective

Restore the layer boundary for the category endpoint:

```text
GET /en/blocks/categories/services
```

Known issue:

```text
BlockCategoryResource::resolveSubitems() performs SQL/model querying.
```

Required direction:

```text
Move data preparation/loading to BlockCategoryRepository or a very small justified helper.
Keep BlockCategoryResource as a serializer of already loaded relations.
Preserve the current public JSON response shape.
```

## Non-negotiable constraints

For TASK-BE-001, do not:

```text
- change routes/api.php
- change database schema or migrations
- change frontend files
- rename public JSON keys
- remove subcategories, children, or childs
- redesign BlockAttachMap
- redesign EavContentResolver
- introduce a broad service layer
- refactor unrelated resources
```

## Preferred change style

Use a surgical diff:

```text
- first inspect current code and response contract
- change only target files or tightly justified supporting files
- preserve compatibility before improving naming
- document any uncertainty in the final report
```

## Completion standard

A run is complete when:

```text
- Resource contains no SQL/model querying for subcategories
- Repository prepares required relations/data
- /en/blocks/categories/services remains response-compatible with services.json
- manual regression checks are reported
```
