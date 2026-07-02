# Skill: WS Backend BlockCategoryResource Guardrails

## Use when

Use this skill for work directly involving:

```text
app/Http/Resources/BlockCategoryResource.php
app/Repositories/BlockCategoryRepository.php
```

Primary references:

- `.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md`
- `.agents/info/BE-RESOURCE-BOUNDARY.md`
- `.agents/info/BE-05-resource-layer.md`

## Known problem

`BlockCategoryResource::resolveSubitems()` currently performs database queries through `BlocksCategories::where()` / `with()`.

This violates the accepted backend boundary:

```text
Repository prepares.
Resource serializes.
```

## First-pass objective

```text
Remove SQL/model querying from BlockCategoryResource while preserving the current JSON response shape for /en/blocks/categories/services.
```

## Target files for first backend task

Primary:

```text
app/Repositories/BlockCategoryRepository.php
app/Http/Resources/BlockCategoryResource.php
```

Allowed only if justified:

```text
small private helpers inside these files
small read helper/assembler if it clearly reduces complexity
```

Avoid touching:

```text
routes/api.php
migrations
models relationships unless proven necessary
BlockAttachMap.php
EavContentResolver.php
frontend files
seeders
```

## Public keys to preserve

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

Inside `subcategories[]`, preserve:

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

## Do not rename yet

```text
subcategories → children
childs → children
locale → scope
items → packages
```

Naming cleanup belongs to a separate task.

## Do not redesign yet

```text
- no DB schema changes
- no endpoint split
- no attach/is_singleton migration
- no universal response redesign
- no broad service layer
```

## Acceptable implementation shape

Preferred direction:

```text
1. Extend Repository eager loading for child categories and their EAV content.
2. Make Resource read only already-loaded relations.
3. Keep resolver/attach map usage stable.
4. Preserve services.json-compatible output.
```

## Required report from agent

After code changes, report:

```text
- whether Resource still imports App\Models\BlocksCategories
- whether Resource still calls where/with/load/loadMissing
- what response keys are preserved
- what manual regression should be run
- any unresolved legacy debt: children/subcategories/childs
```
