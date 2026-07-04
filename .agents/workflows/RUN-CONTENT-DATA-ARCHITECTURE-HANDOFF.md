# RUN — Content Data Architecture Handoff

## Goal

Use this workflow when an agent must understand or extend the WS content data layer from JSON source to frontend API payload.

---

## Step 1 — Read core docs

Read in this order:

```text
.agents/info/BE-08-content-seed-pipeline.md
.agents/info/BE-09-eav-database-field-map.md
.agents/info/BE-10-content-source-registry.md
.agents/info/BE-11-seeder-process-map.md
.agents/info/BE-12-api-data-lift-and-resource-flow.md
.agents/contracts/CONTENT-FAMILY-CONTRACTS.md
```

---

## Step 2 — Determine task type

Classify the task as one of:

```text
content JSON production
content JSON normalization
schema/seeder extension
backend read-side/API work
frontend contract handoff
```

---

## Step 3 — Select contract

For JSON content work:

```text
.agents/contracts/CONTENT-JSON-SOURCE-CONTRACT.md
.agents/contracts/CONTENT-FAMILY-CONTRACTS.md
```

For database/seeder work:

```text
.agents/contracts/CONTENT-DATABASE-FIELD-CONTRACT.md
.agents/contracts/CONTENT-SEEDER-CONTRACT.md
```

For frontend handoff:

```text
.agents/contracts/API-FRONTEND-DATA-HANDOFF-DRAFT.md
```

---

## Step 4 — Do not overreach

Unless explicitly requested:

```text
- do not modify DB migrations;
- do not rename legacy keys;
- do not change API envelopes;
- do not run structural seeders;
- do not refactor frontend;
- do not change resources because content JSON changed.
```

---

## Step 5 — Report

Any agent task touching this layer must report:

```text
source files
seeder involved
DB tables affected
API endpoints affected
frontend contract keys affected
manual command to verify
remaining human decisions
```
