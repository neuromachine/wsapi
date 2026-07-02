# Skill: WS Backend Resource Boundary

## Use when

Use this skill for Laravel API work that touches `app/Http/Resources/*`, especially category, block, or EAV response serialization.

Primary references:

- `.agents/info/BE-04-repository-layer.md`
- `.agents/info/BE-05-resource-layer.md`
- `.agents/info/BE-RESOURCE-BOUNDARY.md`

## Core rule

```text
Repository prepares.
Resource serializes.
Resolver transforms EAV.
AttachMap routes block output.
```

A Resource must not compensate for an incomplete model. If a Resource needs missing relations or filtered collections, fix the Repository/read-side preparation instead of querying inside the Resource.

## Resource may do

```text
- return explicit public JSON fields
- read already-loaded model attributes
- read already-loaded relations
- call EavContentResolver on already-loaded collections
- call BlockAttachMap as routing policy
- map prepared data to API response shape
- use private pure helper methods for serialization clarity
```

## Resource must not do

```text
- call Model::where(), query(), first(), get(), with(), load(), loadMissing()
- call Repository classes
- decide what data should be fetched from DB
- perform locale/scope filtering
- perform recursive tree cleanup that depends on DB state
- silently change public JSON keys
- use attributesToArray() as the API contract for public responses
```

## Preferred Resource shape

```php
return [
    'id' => $this->id,
    'key' => $this->key,
    'name' => $this->name,
    // explicit fields only
];
```

Avoid:

```php
return array_merge($this->attributesToArray(), [...]);
```

unless the task explicitly accepts implicit field leakage.

## Boundary smell checklist

Before editing a Resource, look for:

```text
- App\Models\* imports inside Resource
- ::where() / ->where() chains inside Resource
- ->with() inside Resource
- load/loadMissing inside Resource
- locale filtering inside Resource
- sorting/filtering based on business rules inside Resource
- hidden contract via attributesToArray()
```

If found, mark it as a boundary violation and keep the public response shape stable while moving preparation to the Repository or a narrowly-scoped read assembler.

## Output expectation for agents

When changing Resource code, report:

```text
- what public JSON shape is preserved
- what loading/query responsibility was moved or left unchanged
- what files changed and why
- whether manual regression against endpoint payload is still required
```
