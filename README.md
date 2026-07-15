# Stage 16 — Content Data Architecture Package

This package consolidates the current WS content data architecture after the backend refactor, seeding inventory, locale expansion, offer normalization, and CP generation work.

It is designed for placement into the repository root and for reuse by agent systems.

## Main purpose

Describe the full data path:

```text
JSON source files
  -> Laravel seeders
    -> EAV database tables
      -> Eloquent models / repositories
        -> EavContentResolver / assemblers / resources
          -> API payload
            -> Vue frontend consumption
```

## Main files

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

.agents/workflows/RUN-CONTENT-DATA-ARCHITECTURE-HANDOFF.md
.agents/tasks/TASK-DOC-001-content-data-architecture-finalization.md
```

## How to use

For agents working on content JSON:

1. Read `BE-08-content-seed-pipeline.md`.
2. Read `CONTENT-JSON-SOURCE-CONTRACT.md`.
3. Read the relevant content family section in `CONTENT-FAMILY-CONTRACTS.md`.
4. Modify JSON only.
5. Run only the appropriate content seeder.

For agents working on backend data flow:

1. Read `BE-09-eav-database-field-map.md`.
2. Read `BE-12-api-data-lift-and-resource-flow.md`.
3. Read `CONTENT-DATABASE-FIELD-CONTRACT.md`.
4. Preserve API response contracts.

For agents preparing frontend handoff:

1. Read `API-FRONTEND-DATA-HANDOFF-DRAFT.md`.
2. Treat it as draft / prototype until the frontend contract pass is explicitly performed.
