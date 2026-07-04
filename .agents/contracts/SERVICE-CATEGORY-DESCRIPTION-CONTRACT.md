# SERVICE-CATEGORY-DESCRIPTION-CONTRACT

## Scope

This contract applies to service category description files:

```text
storage/app/blocks/cat/*.json
storage/app/blocks/cat/{group}/*.json
```

These files affect category pages and service catalog navigation/description payloads.

## Goal

Ensure English category descriptions exist and are meaningful for the service catalog.

## Rules

- Preserve existing category keys.
- Preserve legacy keys used by frontend/API, including `childs` where present.
- Do not rewrite tree structure unless the task explicitly asks for structure changes.
- English copy should be concise, service-oriented, and useful for frontend category rendering.
- Vietnamese may remain hollow/placeholder unless explicitly requested.

## Content quality

English category content should answer:

```text
What is this service direction?
Who is it for?
What business result does it support?
```

Avoid overlong generic SEO text in category files unless the file already uses that style.
