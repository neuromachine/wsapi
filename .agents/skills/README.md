# WS Backend Skills Package

This package contains backend-focused agent skills for the WebSolutions Laravel API refactor stage.

Current focus:

```text
Surgical Variant A:
Move category child/subcategory data preparation out of BlockCategoryResource and into Repository/read-side preparation, while preserving the current public JSON response shape.
```

Included skills:

```text
ws_backend_resource_boundary.md
ws_backend_category_contract.md
ws_backend_eav_mapping.md
ws_backend_repository_loading.md
ws_backend_block_category_guardrails.md
ws_backend_manual_regression.md
```

These files are not tasks. They provide reusable constraints and context for future Codex / Antigravity runs.

Expected location:

```text
project-root/.agents/skills/
```

Related method/context files:

```text
project-root/.agents/info/BE-00-overview.md
project-root/.agents/info/BE-03-eav-domain.md
project-root/.agents/info/BE-04-repository-layer.md
project-root/.agents/info/BE-05-resource-layer.md
project-root/.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md
project-root/.agents/info/BE-RESOURCE-BOUNDARY.md
```
