# Skill: WS Backend Repository and Query Loading

## Purpose

Use this skill when changing Laravel Repository classes, eager loading, locale filtering, or model graph preparation.

The Repository/query layer is responsible for preparing the data graph that Resources serialize.

Core rule:

```text
Return a complete-enough model graph for the requested API response.
```

---

## Required context

Read:

```text
.agents/info/BE-04-repository-layer.md
.agents/info/BE-02-blocks-read-side-flow.md
.agents/info/BE-03-eav-domain.md
.agents/info/BE-01-routing-and-scope.md
.agents/info/improvements/AIP-BE-001-read-side-refactor.md
```

---

## Repository responsibilities

Repositories may handle:

```text
- SQL and Eloquent query construction
- eager loading
- locale filtering
- relation selection
- recursive or tree preparation
- sorting when it belongs to data retrieval
- returning prepared Eloquent models or read structures
```

Repositories should make it unnecessary for Resources to query the database again.

---

## Loading strategy

Before adding eager loading, identify which response zones need which relations.

For a category endpoint, possible response zones include:

```text
content
sections
subcategories
blocks
children
```

Each zone may require different relations:

```text
category.blocks
category.blocks.items
category.blocks.items.propertyValues
category.blocks.items.propertyValues.property
category.children
category.children.items
category.children.items.propertyValues
category.children.items.propertyValues.property
```

Do not add relations blindly. Load what the response needs.

---

## Locale filtering

The current route segment `{locale}` is used by backend block reads as a locale filter for EAV property values.

Repository/query layer may filter:

```text
propertyValues.locale = current locale
```

Do not move locale filtering into Resources.

Do not rename route `{locale}` to `scope` or `section` in a refactor task unless explicitly approved.

---

## Category children and subcategories

For category endpoints, children may serve multiple purposes:

```text
- structural category tree
- frontend subcategories list
- legacy compatibility output
```

Do not assume `children`, `subcategories`, and `childs` are interchangeable.

Repository can prepare relations used to build these outputs, but public naming must be preserved by serialization/compatibility layers.

---

## Avoiding God Repository

The Repository should not become a huge response-building class.

If query preparation grows too complex, consider a small collaborator:

```text
CategoryQueryBuilder
BlockCategoryGraphLoader
BlockCategoryPayloadBuilder
```

Choose such a collaborator only when it improves clarity.

Do not introduce large abstractions by default.

---

## Safe refactor examples

Acceptable changes:

```text
- add missing eager-loaded relations used by Resource
- extract repeated query constraints into private methods
- make locale constraints consistent
- prepare child category items so Resources do not query them
- document relation expectations in method names/comments
```

Risky changes:

```text
- changing relation definitions without checking all consumers
- changing default sort order without contract review
- removing whereHas filters because they look redundant
- broad recursive loading that may create heavy queries
- mixing seeder/import concerns into read-side Repository
```

---

## Performance caution

EAV queries can become heavy. When adding eager loading, consider:

```text
- number of categories
- number of items
- number of property values
- locale filtering
- whether recursion is actually needed
- whether the endpoint needs all blocks or only specific blocks
```

Do not over-load every relation “just in case”.

---

## Reporting requirements

When changing repositories, report:

```text
- which relations are now prepared
- why each relation is needed
- whether the Resource no longer needs to query
- whether locale filtering remains correct
- any performance risk
- any remaining relation debt
```

