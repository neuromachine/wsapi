# BE-04 — Repository Layer

## Status

Draft context document for the Repository layer in the WebSolutions backend.

This document is focused on the read-side category endpoint and the accepted surgical direction for `BlockCategoryRepository`.

## Layer purpose

The Repository layer is the backend's data preparation layer.

It is responsible for:

```text
- SQL / Eloquent queries
- eager loading
- locale filtering
- relation completeness
- whereHas constraints
- recursive tree cleanup when needed
- preparing model graphs for Resources
```

It is not responsible for:

```text
- final JSON serialization
- frontend naming decisions beyond preparing required relations
- direct response formatting
- rendering content
```

## Boundary rule

The main rule:

```text
Repository returns a fully prepared model graph.
Resource serializes that graph without performing SQL.
```

This boundary is currently the most important backend refactoring concern.

## Current target repository

File:

```text
app/Repositories/BlockCategoryRepository.php
```

Relevant methods:

```php
getCategoriesRecursive(string $locale, string $key)
getCategory(string $locale, string $key)
```

## Current `getCategory()` behavior

Current flow:

```text
1. Find category by key.
2. Reload category by id with relations.
3. Eager load blocks.
4. Eager load root-category block items for current category.
5. Filter item property values by locale.
6. Load property descriptors.
7. Load direct children filtered by having localized item property values.
```

Current relevant relation loading:

```text
blocks.properties
blocks.items
blocks.items.propertyValues
blocks.items.propertyValues.property
children
```

Current root item loading is category-specific:

```text
blocks.items
  where category_id = root category id
  whereHas propertyValues.locale = current locale
```

This is correct for root category content.

## Current gap

The current repository loads `children`, but does not prepare each child with the same level of item/property data required by the public `subcategories` response.

As a result, `BlockCategoryResource::resolveSubitems()` performs extra database queries for each child category.

Current gap formula:

```text
children are loaded as category rows,
but child.items.propertyValues.property is not loaded for subcategory content serialization.
```

## Accepted surgical direction

Variant A:

```text
Extend BlockCategoryRepository so direct children are loaded together with the item/property data required for subcategories.
```

Goal:

```text
BlockCategoryResource::resolveSubitems()
  should use $this->children and their already-loaded items.
```

Not goal:

```text
Do not rewrite the entire blocks module.
Do not introduce new API shape.
Do not change DB schema.
Do not move to a full service/assembler architecture unless necessary.
```

## Desired prepared model graph for control endpoint

For:

```text
GET /en/blocks/categories/services
```

Repository should provide a category model with enough loaded data for:

```text
content
sections
subcategories
blocks
```

Minimum desired relation state:

```text
category
  blocks
    properties
    items filtered by category_id = category.id and locale
      propertyValues filtered by locale
        property

  children filtered by having localized content
    items filtered by category_id = child.id and locale
      propertyValues filtered by locale
        property
```

Important: this describes the target relation completeness, not exact implementation syntax.

## Possible repository implementation direction

A minimal implementation may use nested eager loading inside `children`:

```php
'children' => function ($q) use ($locale) {
    $q->whereHas('items.propertyValues', function ($deep) use ($locale) {
        $deep->where('locale', $locale);
    })->with([
        'items' => function ($items) use ($locale) {
            $items->whereHas('propertyValues', function ($pv) use ($locale) {
                $pv->where('locale', $locale);
            });
        },
        'items.propertyValues' => function ($pv) use ($locale) {
            $pv->where('locale', $locale);
        },
        'items.propertyValues.property',
    ]);
}
```

This is illustrative only. The exact code must be based on actual model relations.

## Important filtering nuance

Root category item loading currently uses:

```text
where category_id = root category id
```

For child subcategories, equivalent filtering must be child-specific:

```text
child.items should belong to that child category.
```

If the `items` relation on `BlocksCategories` already implies `category_id = child.id`, do not duplicate unnecessary filters.

If it does not, the filter must be explicit.

This must be verified in the actual model relation before coding.

## Recommended repository helper extraction

If the eager loading array becomes hard to read, extracting private helpers is acceptable.

Possible private methods:

```php
private function localizedPropertyValues(string $locale): Closure
private function localizedItems(string $locale): Closure
private function categoryBlocksRelations(string $locale, BlocksCategories $category): array
private function childCategoryRelations(string $locale): array
```

But do not introduce a broad service layer in the first pass.

Preferred first-pass posture:

```text
small readable repository helpers > large new service architecture
```

## What Repository must not do

Do not make Repository return final arrays for this first pass.

Avoid:

```text
- returning response-ready payload arrays
- duplicating EavContentResolver logic in Repository
- encoding frontend keys like title/descr/content manually
- deciding attach output zones
```

Repository should prepare models, not serialize them.

## Manual regression expectation

After future refactor, compare the response for:

```text
GET /en/blocks/categories/services
```

Check:

```text
- data.content exists and keeps same keys
- data.subcategories count remains the same
- subcategories[] keep id, slug, childs, title, descr, content, metadata, priority
- data.blocks shape remains compatible
- no SQL/model queries remain inside BlockCategoryResource
```

## Known risks

### Over-eager loading

Risk:

```text
Loading too much recursive data for all category endpoints.
```

Mitigation:

```text
Keep first pass focused on direct children required by /services subcategories.
Do not enable childrenRecursive broadly unless required.
```

### Relation misunderstanding

Risk:

```text
Duplicate filters or wrong whereHas paths if model relations are misunderstood.
```

Mitigation:

```text
Inspect BlocksCategories model before editing.
Verify whether children->items already scopes by category_id.
```

### Shape drift

Risk:

```text
Repository change causes Resource output shape to drift.
```

Mitigation:

```text
Use services.json as manual baseline.
No public key rename in first pass.
```

## First-pass repository conclusion

The Repository layer is the right place to fix the immediate problem.

Target statement:

```text
BlockCategoryRepository::getCategory() must return a category graph complete enough for BlockCategoryResource to serialize content, sections, subcategories, and blocks without additional database access.
```
