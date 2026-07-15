# Skill: WS Backend Repository Loading

## Use when

Use this skill for Laravel repository changes, especially:

```text
app/Repositories/BlockCategoryRepository.php
app/Repositories/BlockRepository.php
app/Repositories/BlockItemRepository.php
```

Primary references:

- `.agents/info/BE-04-repository-layer.md`
- `.agents/info/BE-RESOURCE-BOUNDARY.md`
- `.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md`

## Core rule

```text
Repository is the only layer that prepares data from DB.
```

Repository owns:

```text
- SQL / Eloquent queries
- eager loading
- locale filtering
- whereHas constraints
- recursive tree cleanup
- preparing complete relation graphs for Resources
```

## Resource handoff rule

Before returning a model to Resource, Repository must prepare enough data for Resource to serialize without additional queries.

For category endpoint work, this means root category and relevant child/subcategory data must be already available.

## Control target

```text
BlockCategoryRepository::getCategory($locale, $slug)
```

Control endpoint:

```text
GET /en/blocks/categories/services
```

## Minimal safe direction for first pass

Surgical Variant A:

```text
- keep endpoint and response shape unchanged
- extend/prefer Repository loading over Resource querying
- move child/subcategory preparation out of BlockCategoryResource::resolveSubitems()
- preserve BlockAttachMap behavior
- preserve EavContentResolver behavior
```

## Repository may do

```text
- eager-load children and their items/propertyValues/property
- use whereHas for locale-specific values
- sort or cleanup prepared collections when required by public contract
- introduce private methods for relation arrays or child preparation
```

## Repository should avoid

```text
- returning partially prepared models that force Resource to query
- mixing response formatting deeply into Repository
- creating a broad service layer without task approval
- changing API shape while fixing loading
```

## Naming guidance

Use clear private helpers if necessary:

```php
private function categoryRelations(string $locale): array
private function childCategoryRelations(string $locale): array
```

But do not over-abstract.

## Success condition

After the first pass:

```text
BlockCategoryResource can build content/subcategories/blocks from already-loaded data.
No BlocksCategories::where() or with() call remains inside Resource.
GET /en/blocks/categories/services remains response-compatible.
```
