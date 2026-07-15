# TASK-DOC-001 — Content Data Architecture Finalization

## Status

Documentation / architecture consolidation task.

## Goal

Finalize repository documentation for the WS content data layer after backend refactor, seed-layer inventory, service-offer localization, category-description localization, and CP batch generation.

## Scope

This task is documentation-only unless explicitly changed.

Allowed files:

```text
.agents/info/*.md
.agents/contracts/*.md
.agents/workflows/*.md
.agents/reports/*.md
```

Do not modify:

```text
app/**
database/**
storage/app/blocks/**
src/**
routes/**
config/**
```

## Required consolidation topics

Document:

```text
- JSON source paths;
- seeder responsibilities;
- EAV table field roles;
- locale model;
- safe vs dangerous seeders;
- content family contracts;
- EAV lifting into API payload;
- draft frontend handoff contract;
- current content production status.
```

## Expected deliverables

```text
.agents/info/BE-08-content-seed-pipeline.md
.agents/info/BE-09-eav-database-field-map.md
.agents/info/BE-10-content-source-registry.md
.agents/info/BE-11-seeder-process-map.md
.agents/info/BE-12-api-data-lift-and-resource-flow.md
.agents/info/BE-13-content-production-status.md
.agents/contracts/CONTENT-DATABASE-FIELD-CONTRACT.md
.agents/contracts/CONTENT-JSON-SOURCE-CONTRACT.md
.agents/contracts/CONTENT-SEEDER-CONTRACT.md
.agents/contracts/API-FRONTEND-DATA-HANDOFF-DRAFT.md
.agents/contracts/CONTENT-FAMILY-CONTRACTS.md
```

## Success criteria

```text
- A future agent can understand how content JSON becomes API data.
- A future agent can choose the correct seeder for a content family.
- A future agent understands which DB fields are internal and which become API keys.
- The frontend-contract draft is clearly marked as draft.
- Dangerous seeders are clearly flagged.
```
