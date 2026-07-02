# Skill: WS Backend EAV Mapping

## Use when

Use this skill when work touches:

```text
app/Support/EavContentResolver.php
app/Support/BlockAttachMap.php
app/Http/Resources/BlockResource.php
app/Http/Resources/BlockItemResource.php
app/Http/Resources/BlockCategoryResource.php
```

Primary references:

- `.agents/info/BE-03-eav-domain.md`
- `.agents/info/BE-05-resource-layer.md`

## Domain model

Current backend content model:

```text
Block              = logical entity type
BlockItem          = entity instance
BlockItemProperty  = field/schema descriptor
PropertyValue      = value for item/property/locale/version
Category           = hierarchical placement/grouping
```

Database layers:

```text
STRUCTURE: blocks, blocks_categories
ENTITY:    block_items
SCHEMA:    block_item_properties
DATA:      block_item_property_values
```

## Frontend contract principle

Frontend must receive denormalized logical JSON, not EAV internals.

Do not leak:

```text
property_id
item_id
value_id
pivot rows
raw value_type plumbing
EAV join structure
```

## EavContentResolver role

`EavContentResolver` is the current standard transform point:

```text
Collection<BlockItem>
  → flat object / keyed object / array of objects
```

Supported conceptual modes:

```text
single = true   → first item as object
keyed = true    → item.key → object
array mode      → list of flat objects
```

## Casting

Respect `value_type` casting:

```text
json
integer
boolean
float
number
string/default
```

Do not introduce another incompatible casting implementation unless the task explicitly refactors all consumers.

## Collection fields

Respect `BlockItemProperty.is_collection`:

```text
false → scalar
true  → array
```

## Sort behavior

Current resolver detects `sort` property and sorts collections if applicable. Treat this as transitional behavior.

Do not redesign sorting in the first `BlockCategoryResource` boundary pass.

## AttachMap role

`BlockAttachMap` is a temporary routing policy for block output placement.

Current conceptual responsibilities:

```text
- attach block to content / sections / blocks
- decide single vs array
- decide keyed output where configured
```

Do not move `BlockAttachMap` into database columns during a surgical Resource/Repository pass.

## Agent guardrails

```text
- use EavContentResolver instead of hand-rolling property flattening
- do not modify EavContentResolver unless the task explicitly targets it
- do not leak EAV internals to frontend response
- do not change BlockAttachMap policy during category boundary cleanup
- preserve existing output shape first
```
