# Skill: WS Backend Manual Regression

## Use when

Use this skill after backend changes that affect category endpoints or Resource output.

Primary references:

- `.agents/info/BE-CATEGORY-ENDPOINT-CONTRACT.md`
- `.agents/info/BE-RESOURCE-BOUNDARY.md`

## Control endpoint

```text
GET /en/blocks/categories/services
```

Expected frontend route consumer:

```text
/en/services
```

## Baseline payload

Use the saved Postman payload `services.json` as the current response reference.

The goal of the first backend pass is compatibility, not redesign.

## Manual checks

After code changes, compare before/after response shape.

Check root:

```text
- response.data.data exists
- data.id exists
- data.key === services
- data.content exists
- data.sections exists
- data.subcategories exists
- data.blocks exists
- data.children key still exists
```

Check content:

```text
- data.content.title
- data.content.descr
- data.content.content
- data.content.metadata
- data.content.priority
```

Check subcategories:

```text
- data.subcategories is an array
- count is not unexpectedly reduced
- first item keeps id
- first item keeps slug
- first item keeps childs
- first item keeps title
- first item keeps descr
- first item keeps content
- first item keeps metadata
- first item keeps priority
```

Check blocks:

```text
- data.blocks key remains present
- block item shape is not redesigned
```

## Static code check

Search target Resource for forbidden patterns:

```text
BlocksCategories::
::where(
->where(
->with(
->load(
->loadMissing(
```

A Resource may use collection operations on already-loaded relations, but it must not initiate SQL.

## Optional commands

Run what is available and appropriate for the environment:

```bash
php artisan test
composer test
vendor/bin/pint --test
```

If tests are not configured for this endpoint, do not invent a broad test system during the surgical pass. Report manual regression instead.

## Report format

```text
Manual regression:
- endpoint checked:
- response shape preserved: yes/no
- subcategories preserved: yes/no
- blocks preserved: yes/no
- Resource SQL removed: yes/no
- unresolved risks:
```
