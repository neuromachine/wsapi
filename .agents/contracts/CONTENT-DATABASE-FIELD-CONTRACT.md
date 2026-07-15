# CONTRACT — Content Database Field Contract

## Purpose

Machine-readable contract describing how content data must be stored in the current EAV database model.

---

## Contract rules

```yaml
identity:
  business_identity: key
  unstable_identity: id
  rule: "Agents should use key for content identity. DB ids are internal."

localization:
  field: block_item_property_values.locale
  supported_current: [ru, en, vi]
  default_authoring_locale: ru
  rule: "Route locale filters property values. Do not store wrong locale values."

typing:
  schema_type_field: block_item_properties.type
  runtime_value_type_field: block_item_property_values.value_type
  rule: "value_type controls API casting; property.type guides schema/admin expectations."

frontend_visibility:
  visible_after_flattening:
    - block_item_properties.key
    - decoded/cast block_item_property_values.value
  hidden:
    - property_id
    - item_id
    - value_id
    - value_type
    - block_id
    - category_id
```

---

## Table contracts

### `blocks`

```yaml
blocks:
  purpose: "Logical content type / block schema."
  key:
    role: "stable block type identifier"
    examples: [descr_data, offers, ind_offers, navigation, pages, portfolio]
    frontend_contract: "sometimes exposed as block metadata, but mostly backend routing"
  name:
    role: "human-readable admin label"
  description:
    role: "admin/documentation description"
```

### `blocks_categories`

```yaml
blocks_categories:
  purpose: "Hierarchy / placement / taxonomy."
  key:
    role: "stable category identifier; often behaves like slug"
    frontend_aliases: [key, slug]
  name:
    role: "category label"
    frontend_aliases: [name, title]
  description:
    role: "raw short description"
  content:
    role: "raw category content; may be superseded by assembled EAV data.content"
  parent_id:
    role: "tree hierarchy"
```

### `block_items`

```yaml
block_items:
  purpose: "Concrete content entity."
  key:
    role: "stable item key / slug"
    examples: [visa_run_agency, posadocnaia-stranica, about, main-en]
  block_id:
    role: "which block schema this item belongs to"
  category_id:
    role: "where this item is attached"
  name:
    role: "display/admin item name"
  description:
    role: "optional item description"
```

### `block_item_properties`

```yaml
block_item_properties:
  purpose: "Schema field definition for a block."
  key:
    role: "future flattened JSON key"
    examples: [title, descr, content, hero, benefits, items, includes, important]
    warning: "This is public API surface. Do not rename casually."
  type:
    role: "expected logical type"
    examples: [string, text, html, json, number, boolean]
  is_collection:
    role: "controls array-vs-scalar output when multiple values exist"
  meta:
    role: "future extension metadata"
```

### `block_item_property_values`

```yaml
block_item_property_values:
  purpose: "Actual localized value for a property on an item."
  value:
    role: "raw scalar or JSON-encoded value"
  value_type:
    role: "runtime cast rule"
  locale:
    role: "scope/locale filter"
  version:
    role: "reserved for future draft/versioning"
```

---

## Content identity rule

Never build new content workflows around numeric IDs unless there is no alternative.

Preferred identity chain:

```text
block.key + category.key + item.key + property.key + locale
```

This chain is stable across local DB rebuilds.
