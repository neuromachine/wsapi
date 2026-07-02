# Skill: WS Backend EAV Mapping

## Purpose

Use this skill when working with EAV content transformation in the Laravel backend.

The project stores flexible content using Blocks, Items, Properties, and Property Values. The frontend should receive flat, logical objects, not raw EAV internals.

Core rule:

```text
EAV is storage structure. API returns logical content objects.
```

---

## Required context

Read:

```text
.agents/info/BE-03-eav-domain.md
.agents/info/BE-06-eav-content-resolver.md
.agents/info/BE-02-blocks-read-side-flow.md
.agents/info/FE-BACKEND-CONTRACT-BRIDGE.md
```

---

## EAV model summary

The current backend model includes:

```text
Block
  → BlockItem
    → BlockItemPropertyValue
      → BlockItemProperty
```

Logical meaning:

```text
Block             = type/group of content
BlockItem         = item/entity instance
BlockItemProperty = property schema/descriptor
PropertyValue     = localized value
```

Important fields:

```text
property.key
property.is_collection
property.meta
property_value.value
property_value.value_type
property_value.locale
property_value.version
```

---

## EavContentResolver role

`EavContentResolver` should remain a pure transformation helper.

It may:

```text
- receive already-loaded BlockItem collections
- flatten values by property key
- cast values by value_type
- respect is_collection
- return single / keyed / array output modes
- apply current compatibility sorting behavior
```

It must not:

```text
- query the database
- decide which category/block should be loaded
- decide public endpoint shape
- become a repository or service
- know frontend components
```

---

## Output modes

Common resolver modes:

```text
single = true
  first item → flat object

keyed = true
  item.key → flat object map

single = false and keyed = false
  list of flat objects
```

Preserve behavior unless a task explicitly allows changing it.

---

## Value casting

Respect current `value_type` behavior.

Typical mappings:

```text
json    → decoded array/object
integer → int
boolean → bool
float   → float
number  → numeric value
html    → string / trusted HTML boundary for frontend
string  → string
text    → string
```

Do not change casting semantics casually. It can change frontend behavior.

---

## Collection values

If `property.is_collection` is true, multiple values for the same property key should become an array.

If false, the property should resolve to a scalar/latest assigned value according to current resolver behavior.

Do not flatten collection properties into scalar values.

---

## Avoid EAV leaks

The API should not expose raw EAV implementation details unless explicitly intended.

Avoid leaking:

```text
property_id
item_id
property internal names
pivot details
raw property value rows
```

Frontend should consume logical keys:

```text
title
descr
content
metadata
priority
hero
benefits
items
includes
```

---

## Duplicated mapping caution

If `BlockItemResource` and `EavContentResolver` perform similar flattening in different ways, do not randomly unify them during unrelated tasks.

A safe unification requires:

```text
- inspect all Resource usages
- compare output shapes
- preserve public contract
- document behavior differences
```

If unification is out of scope, report it as technical debt.

---

## Sorting caution

Current EAV sorting may be driven by fields such as:

```text
priority
sort
```

Do not change ordering behavior unless the task explicitly targets sorting.

If sorting currently lives in Resource or Resolver and should move later, document that as future improvement.

---

## Reporting requirements

When changing EAV mapping, report:

```text
- which output mode is used
- whether value casting changed
- whether collection behavior changed
- whether ordering changed
- whether raw EAV data leaks were introduced or removed
- which endpoints/resources may be affected
```

