# BE-01 — Routing and Scope Layer

## Status

Draft architecture context.

## Purpose

This document describes the current backend routing and scope model for the WebSolutions Laravel API. It focuses on the practical behavior of `{locale}` in the API routes, how it relates to frontend `scope`, and why this area must be handled carefully during refactoring.

This is not a task file. This is context for human review and future code-agent work.

---

## 1. Current routing model

The backend exposes a Laravel JSON API consumed by the Vue SPA frontend.

The currently important public route pattern is:

```text
/{locale}/blocks/categories/{slug}
```

The control endpoint for the current backend read-side discussion is:

```text
GET /en/blocks/categories/services
```

This endpoint is consumed by the frontend page:

```text
/en/services
```

The simplified request chain is:

```text
Frontend route
  /en/services

Frontend fetch
  /en/blocks/categories/services

Backend route
  Route::prefix('{locale}')
    → blocks/categories/{slug}
      → BlockCategoryController
        → BlockCategoryRepository
          → BlockCategoryResource
            → JSON response
```

The Laravel response is wrapped in the standard resource envelope:

```text
response.data.data
```

The frontend expects actual payload under `data`.

---

## 2. `{locale}` is currently overloaded

The route segment `{locale}` currently plays more than one role.

### 2.1 API route prefix

It is part of the public API URL:

```text
/en/blocks/categories/services
/ru/blocks/categories/services
/vi/blocks/categories/services
```

### 2.2 Application locale

For form handling and validation messages, `{locale}` is used as Laravel application locale.

The middleware sets application language for localized responses:

```text
SetLocale
  → App::setLocale($request->route('locale'))
```

This matters for validation messages and translated API messages.

### 2.3 EAV value filter

For the Blocks / EAV subsystem, `{locale}` is also used as a data filter:

```text
block_item_property_values.locale = {locale}
```

For example, when `/en/blocks/categories/services` is requested, EAV property values should be filtered to `locale = en`.

This affects:

```text
blocks.items.propertyValues
children.items.propertyValues
BlockItem properties
category content
subcategories content
```

---

## 3. Frontend `scope` is broader than backend `locale`

On the frontend, the current URL prefix is increasingly treated as `scope` rather than just language.

Examples of possible future scopes:

```text
/en
/ru
/vi
/pattaya
/nha-trang
/brand-a
/version-b
```

In the current backend, the route parameter is still named `{locale}` and should not be renamed mechanically.

The conceptual difference:

```text
locale
  current backend meaning: language code and EAV value filter

scope
  frontend meaning: URL/application context, possibly wider than language

section
  future possible backend meaning: data selection context; may include language, city, brand, site version, market segment, or other partition
```

---

## 4. Do not rename `locale` during ordinary refactoring

Changing `{locale}` to `{scope}` or `{section}` is not a safe mechanical refactor.

It may affect:

```text
routes/api.php
SetLocale middleware
FormSubmitRequest validation language
Repository filters
property_values.locale
frontend API calls
frontend route construction
navigation links
stored content metadata
seeders
```

Therefore, any rename must be treated as a separate migration task.

During ordinary read-side refactoring, keep the current public route behavior unchanged:

```text
/{locale}/blocks/categories/{slug}
```

---

## 5. Current safe interpretation

For the current backend read-side refactor work, use this interpretation:

```text
{locale} in route
  → current API prefix
  → Laravel app locale
  → EAV property value locale filter
```

The frontend may call the same concept `scope`, but backend code should continue to respect the existing `{locale}` contract until a dedicated migration is approved.

---

## 6. Impact on `BlockCategoryRepository`

The Repository is the proper layer to apply locale filtering for read-side category endpoints.

Allowed in Repository:

```text
- whereHas propertyValues where locale = $locale
- eager load propertyValues filtered by locale
- prepare root category graph for the requested locale
- prepare children/subcategories for the requested locale
```

Not recommended in Resource:

```text
- filtering property values by locale
- issuing queries with locale constraints
- deciding which localized records exist
- loading fallback data
```

The Resource should receive a prepared model graph or read structure.

---

## 7. Impact on `BlockCategoryResource`

`BlockCategoryResource` may include a field named `section` in its JSON output.

Current behavior:

```text
section = route/app locale value used for the response context
```

This field must not be removed or renamed without a frontend compatibility plan.

Do not replace it with `scope` in this task family.

---

## 8. Control endpoint contract

For the current backend documentation and task planning, the control endpoint is:

```text
GET /en/blocks/categories/services
```

Its response is used by the frontend services page and must remain compatible.

Do not break:

```text
data.content
data.sections
data.subcategories
data.blocks
data.children
data.section
```

The exact meaning of some fields may be imperfect or transitional, but they are currently part of the response surface.

---

## 9. Compatibility-first rule

Backend route/scope refactoring should follow this rule:

```text
Keep the public route and response shape stable first.
Clarify terminology second.
Rename only through a planned compatibility migration.
```

This means:

```text
- do not rename route params casually
- do not remove `section`
- do not remove `locale` filtering
- do not replace `locale` with `scope` in backend code unless the task explicitly asks for it
- document naming debt instead of silently fixing it
```

---

## 10. Known naming debt

Current naming debt includes:

```text
locale vs scope vs section
children vs subcategories vs childs
acticle vs article
items vs section items vs block items
```

These should be handled as compatibility-layer / migration topics, not as incidental cleanup in ordinary read-side refactoring.

---

## 11. Recommended future direction

A possible future architecture may introduce a clearer concept such as:

```text
RequestContext
  locale: en
  scope: en
  section: services
  market: null
  version: published
```

or:

```text
ContentScope
  language
  site section
  market/city
  publication status
```

However, this is a future design decision.

The current safe state is:

```text
keep `{locale}` as the route-level API context
filter EAV values by `$locale`
preserve frontend response fields
```

---

## 12. Instructions for future agents

When working on backend read-side routing or category endpoints:

```text
1. Inspect routes/api.php before changing endpoint behavior.
2. Preserve `/{locale}/...` URLs unless explicitly instructed otherwise.
3. Treat `locale` as current API contract, not just an internal variable.
4. Do not rename `section` in JSON output.
5. Do not alter frontend route assumptions.
6. Apply locale filtering in Repository/query layer, not in Resource.
7. Report any terminology ambiguity instead of silently resolving it.
```

---

## 13. Summary

The routing/scope layer is transitional.

Current backend terminology says `locale`. Current frontend direction increasingly says `scope`. Future architecture may need a richer `section` or `context` model.

For now, the correct behavior is:

```text
preserve route contract
preserve response shape
filter localized EAV data in Repository
avoid naming migrations inside refactor tasks
```
