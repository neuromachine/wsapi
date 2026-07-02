# BE-03 — EAV Domain Model

## Status

Draft context document for the backend EAV domain model used by the WebSolutions blocks module.

This document describes the known data model and its role in the current category endpoint. It does not propose database migrations and does not define a concrete refactoring task.

## Domain summary

The backend uses a block/category content model with an EAV layer.

Core concepts:

```text
Block
  logical content/entity type

BlockItem
  concrete entity/content item belonging to a block

BlockItemProperty
  schema descriptor for a property of a block item

BlockItemPropertyValue
  localized/versioned value for a property on an item

BlocksCategories
  hierarchical placement/taxonomy structure
```

The goal of the EAV layer is to allow dynamic content structures without changing database schema for each new content type.

## Logical layers

```text
STRUCTURE LAYER
  blocks
  blocks_categories

ENTITY LAYER
  block_items

SCHEMA LAYER
  block_item_properties

DATA LAYER
  block_item_property_values
```

## Main tables

### `blocks`

Represents a logical type of content.

Known fields:

```text
id
key
name
description
created_at
updated_at
```

Examples of known block keys:

```text
descr_data
hero
services
works
navigation
ind_offers
```

Not all keys are equally relevant to the current task.

For the control endpoint `/en/blocks/categories/services`, `descr_data` is especially important because it provides the category content object.

### `blocks_categories`

Represents category hierarchy / placement.

Known fields:

```text
id
key
name
description
content
parent_id
created_at
updated_at
```

For `/en/blocks/categories/services`:

```text
services
  -> root category for Services page

prodvizenie / razrabotka / ...
  -> child categories used as subcategories in API response
```

### `block_items`

Represents concrete items belonging to a block and optionally to a category.

Known fields:

```text
id
block_id
category_id
key
name
description
created_at
updated_at
```

Important current use:

```text
A category gets its logical content by selecting block_items attached to the category and block.
```

Example:

```text
category: services
block: descr_data
item: services
properties: title, descr, content, metadata, priority
```

### `block_item_properties`

Represents property schema for a block.

Known fields:

```text
id
block_id
key
name
type
is_required
is_collection
is_unique
meta
created_at
updated_at
```

Important current properties for `descr_data`-style content:

```text
title
descr
content
metadata
priority
```

Important current properties for individual offer / compred-style content:

```text
title
content
acticle
items
hero
benefits
includes
extras
important
```

Note: `acticle` is known typo/legacy naming and must not be silently renamed in backend contract work.

### `block_item_property_values`

Stores the actual EAV values.

Known fields:

```text
id
property_id
item_id
value
value_type
locale
version
created_at
updated_at
```

Important fields:

```text
value_type
  controls casting in EavContentResolver

locale
  controls localized output

version
  reserved / future draft-publish or versioning context
```

## EAV transformation rule

The database is dynamic and normalized. The API response must be logical and denormalized.

Internal shape:

```text
BlockItem
  -> propertyValues[]
    -> property.key
    -> value
    -> value_type
    -> property.is_collection
```

Frontend-oriented output:

```json
{
  "title": "Services",
  "descr": "...",
  "content": "...",
  "metadata": {},
  "priority": "2"
}
```

The frontend must not know:

```text
property_id
item_id
value_id
EAV joins
propertyValues
value_type internals
```

## EavContentResolver role

`app/Support/EavContentResolver.php` is the current central transformer from EAV items to flat arrays.

Current behavior:

```text
resolve(Collection $items, bool $single = true, bool $keyed = false): array
```

Modes:

```text
single = true
  first item -> flat object

keyed = true
  item key -> flat object map

single = false, keyed = false
  list of flat objects
```

Casting behavior:

```text
json     -> json_decode(...)
integer  -> (int)
boolean  -> filter_var(...)
float    -> (float)
number   -> numeric conversion
default  -> raw value
```

Collection behavior:

```text
property.is_collection = true
  -> result[key][] = value

property.is_collection = false
  -> result[key] = value
```

Sorting behavior:

```text
If first item has a property with key = sort,
items are sorted by that numeric value.
```

## Current endpoint-specific EAV use

For `/en/blocks/categories/services`, EAV contributes to several output zones.

### `data.content`

Built from `descr_data` block item for category `services`.

Expected shape:

```text
title
descr
content
metadata
priority
```

### `data.subcategories[]`

Each child category is merged with its own `descr_data`-like EAV content.

Expected shape:

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

Important: `childs` is a public compatibility key and must not be renamed in the first pass.

### `data.blocks[]`

Contains block-level output through `BlockResource`.

Current first block in saved payload:

```text
key: descr_data
content: same logical content as category content
attach: content
properties: [...]
items: [...]
```

## Known domain debt

### Naming debt

```text
section
  currently derived from request locale; future naming may become scope/section context.

subcategories
  current public category summary list.

children
  recursive structural relation, current use uncertain for services endpoint.

childs
  legacy public key inside subcategories; keep for compatibility.

acticle
  typo/legacy key in compred/ind_offers block properties; do not silently rename.
```

### Sorting debt

Sorting currently appears in more than one place:

```text
EavContentResolver::sortIfNeeded()
BlockCategoryResource::resolveSections()
BlockCategoryResource::resolveSubitems()
```

Target direction:

```text
Sorting should become explicit and consistent, but not in the first surgical pass unless needed to preserve output.
```

### Attach policy debt

`BlockAttachMap` is currently hardcoded policy.

Target direction may be:

```text
blocks.attach
blocks.is_singleton
property meta for sort behavior
```

But this is not part of the first backend refactor.

## First-pass EAV policy

For the accepted minimal backend refactor:

```text
Do not change EAV tables.
Do not change property keys.
Do not change value_type rules.
Do not change EavContentResolver unless absolutely required.
Do not change BlockAttachMap behavior.
Use EavContentResolver on already-loaded item collections.
Preserve current output shape for services.json.
```

## Practical implication

The first refactor should not redesign the EAV domain. It should make the category model complete enough so that `BlockCategoryResource` can serialize child category EAV content without performing additional SQL.

Target behavior:

```text
BlockCategoryRepository loads child category items and their localized propertyValues.property.
BlockCategoryResource calls EavContentResolver on already-loaded child items.
```
