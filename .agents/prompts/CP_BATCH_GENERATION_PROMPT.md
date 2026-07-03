# Prompt — Batch Generation of Individual Commercial Proposals

Use this prompt when generating multiple `ind_offers` JSON files.

---

## Role

You are a WS content production agent working inside a Laravel/Vue project that uses EAV JSON seed files.

Your task is to generate valid commercial proposal JSON files for the `ind_offers` content type.

---

## Read before acting

```text
.agents/contracts/IND-OFFERS-CP-CONTENT-CONTRACT.md
.agents/contracts/CP-CONTENT-MODEL.md
.agents/templates/cp_batch_input.template.md
```

If available, also inspect existing examples:

```text
storage/app/blocks/blocks/items/ind_offers/visarun_system.json
storage/app/blocks/blocks/items/ind_offers/medical_platform_en.json
storage/app/blocks/blocks/items/ind_offers/default.json
```

---

## Input variables

The human operator will provide:

```text
segment
geography
target audience
output languages
pricing frame
entries[]
```

Each entry must contain at least:

```text
key
name
lead type / target business
location
main problem
must_include
```

---

## Output

For each entry, create one JSON file:

```text
storage/app/blocks/blocks/items/ind_offers/{key}.json
```

Each file must match the `IND-OFFERS-CP-CONTENT-CONTRACT`.

---

## Content rules

```text
- Use root properties for RU/default.
- Use en.properties for English when requested.
- Use vi.properties only if real Vietnamese content is requested.
- Hollow vi.properties = {} is allowed as placeholder.
- Do not create unsupported property keys.
- Do not use final unless the backend property registry is extended.
- Use acticle as the closing statement.
- Keep copy scannable and business-oriented.
- Focus on systems, not just websites.
```

---

## Verification before final response

For every generated JSON:

```text
- parse as valid JSON
- root key matches file name
- block = ind_offers
- root properties includes hero, benefits, items, includes
- pricing packages have index/name/price/term/featured/desc/features
- no unsupported property keys
```

Final response must include:

```text
- files created
- locales covered
- any intentionally hollow locale branches
- seeding command
- endpoint examples
```

