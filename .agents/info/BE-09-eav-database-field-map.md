# BE-09 — EAV Database Field Map

## Purpose

Field-level map for the content data storage layer. This document explains what each core field means, how it is usually filled, and how it appears or does not appear in frontend-facing API payloads.

---

## 1. `blocks`

Represents a logical content type / block schema.

| Field | Purpose | Filled by | Output behavior |
|---|---|---|---|
| `id` | Internal primary key used by relations | DB auto-increment / structural seeders | Internal only unless a block resource exposes it |
| `key` | Stable machine key for the block type, e.g. `offers`, `ind_offers`, `descr_data`, `navigation` | `BlockSeeder`, `BlockForCpDataSeeder`, `BlocksForNavigationSeeder`, helper upserts | May appear as block key in API; used by backend for routing/mapping |
| `name` | Human/admin name | Block schema seeders | May appear in block metadata; not always used by frontend |
| `description` | Human/admin explanation | Block schema seeders | Usually not critical for frontend |
| `created_at`, `updated_at` | Timestamps | DB / seeders | Usually not relevant to UI |

### Notes

`blocks.key` is more important than `blocks.id` for architecture. IDs may change across environments; keys must be treated as the contract.

---

## 2. `blocks_categories`

Represents hierarchy / placement / taxonomy.

| Field | Purpose | Filled by | Output behavior |
|---|---|---|---|
| `id` | Internal primary key and parent/child relation target | Category seeders | Currently exposed in some category payloads; frontend may use it only as stable render key, but key/slug should be preferred |
| `key` | Stable machine key / slug-like category identifier | `categories.json`, `tree.json`, `cat/*.json` seeders | Exposed as `key` or `slug` depending on payload layer |
| `name` | Category display name | Category source JSON / seeders | Exposed as `name` or transformed into `title` in subcategory payloads |
| `description` | Short category description | Category source JSON | May be exposed as `description` or mapped through EAV descr_data into `descr` |
| `content` | Category-level raw content field | Category source JSON | Can exist but API `data.content` is often assembled from `descr_data` EAV content, not simply this column |
| `parent_id` | Category tree parent | Category tree seeders | Used to build recursive hierarchy / `subcategories` |
| `created_at`, `updated_at` | Timestamps | DB / seeders | Explicitly mapped by `BlockCategoryResource`, but usually not UI-critical |

### Notes

`blocks_categories` defines placement. It does not define the schema of a content item. Schema comes from `blocks` + `block_item_properties`.

---

## 3. `block_items`

Represents one concrete content entity belonging to a block and optionally attached to a category.

| Field | Purpose | Filled by | Output behavior |
|---|---|---|---|
| `id` | Internal item ID | Content seeders / `ImportHelper::upsertItem` | May be exposed in item arrays; frontend should not rely on it as business key |
| `block_id` | References `blocks.id` | Seeder resolves by block key | Internal relation; not exposed as business data |
| `category_id` | Optional placement/category relation | Seeder resolves by category key | Internal relation; affects which endpoint includes the item |
| `key` | Stable item key, e.g. service category key, CP key, navigation key | JSON `key` | Exposed as `key`, often used as slug/business identifier |
| `name` | Item display/admin name | JSON `name` / localized name handling varies | Exposed in item payloads or metadata |
| `description` | Optional item description | JSON / seeder | Not consistently used; can be omitted from slim resources |
| `created_at`, `updated_at` | Timestamps | DB / seeders | Usually not UI-critical |

### Notes

`block_items.key` is the primary content identity for JSON-generated content. Generated CP files, service offer packages, pages, and navigation entries should use stable keys.

---

## 4. `block_item_properties`

Represents field/schema definition for a block.

| Field | Purpose | Filled by | Output behavior |
|---|---|---|---|
| `id` | Internal property ID | Schema/content seeders | Internal only |
| `block_id` | Owning block schema | Seeder resolves block | Internal only |
| `key` | Frontend-facing property name after EAV flattening, e.g. `title`, `items`, `hero` | Block schema definitions | Becomes JSON key in flattened API payload |
| `name` | Human/admin label | Schema seeders | Admin/Filament context |
| `type` | Expected logical type, e.g. `string`, `html`, `json`, `number` | Schema seeders | Used by humans/admin; may be mirrored by value values |
| `is_required` | Requirement metadata | Schema seeders | Not strictly enforced everywhere |
| `is_collection` | Whether values should be treated as collection/multiple | Schema seeders | Used by `EavContentResolver` to output array vs scalar |
| `is_unique` | Uniqueness metadata | Schema seeders | Not central to current API output |
| `meta` | Additional schema metadata | Schema seeders / future admin | Reserved for advanced field behavior |
| `created_at`, `updated_at` | Timestamps | DB / seeders | Not frontend-facing |

### Important current property keys

For `descr_data`:

```text
title
descr
content
metadata
priority
```

For `ind_offers` / CP:

```text
title
content
acticle
items
hero
benefits
includes
reelsSystem
extras
important
```

For `offers` service packages, properties are package fields such as:

```text
title
url
price
descr
content
image
files
timeline
features
icon
```

Exact schema depends on block definitions and existing JSON.

---

## 5. `block_item_property_values`

Stores actual localized values.

| Field | Purpose | Filled by | Output behavior |
|---|---|---|---|
| `id` | Internal value ID | DB auto-increment | Internal only |
| `property_id` | References `block_item_properties.id` | Seeder resolves property by key + block | Internal relation |
| `item_id` | References `block_items.id` | Seeder resolves/creates item | Internal relation |
| `value` | Raw value, often string or JSON-encoded data | Seeder from JSON property payload | Cast/decoded by `EavContentResolver` into frontend field value |
| `value_type` | Runtime casting hint: `string`, `json`, `html`, `number`, `boolean`, etc. | Seeder infers from source value or property type | Controls API value casting |
| `locale` | Locale/scope, e.g. `ru`, `en`, `vi` | Seeder locale loop | Repository filters values by route `{locale}` |
| `version` | Reserved for versioning / draft-publish | Currently usually `null` | Not used by frontend yet |
| `created_at`, `updated_at` | Timestamps | DB / seeders | Not frontend-facing |

### Notes

The frontend receives the result of resolving `property.key -> cast(value)`. It should not receive `property_id`, `item_id`, or `value_type`.

---

## 6. Pivot table: `block_item_property_pivot`

Currently present but not central to the modern read/seed pipeline.

| Field | Purpose | Current status |
|---|---|---|
| `item_id` | Item reference | Legacy / optional relation support |
| `property_id` | Property reference | Legacy / optional relation support |

The canonical value relation after refactor is `BlockItem::propertyValues()`.

---

## 7. Field-to-frontend rule

```text
DB field -> backend relation/context -> EAV flattening -> frontend JSON
```

Example:

```text
block_item_properties.key = "hero"
block_item_property_values.value = { ... JSON ... }
block_item_property_values.value_type = "json"
block_item_property_values.locale = "en"

API output:
{
  "hero": { ... decoded JSON ... }
}
```

This means property keys are public API surface. They must be treated as frontend contracts.
