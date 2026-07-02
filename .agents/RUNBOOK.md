# RUNBOOK — WebSolutions Agent Execution Guide

> Purpose: provide a practical step-by-step procedure for running coding-agent tasks in the WebSolutions / WS CODE project.
>
> This document is operational. Use it before, during, and after an agent run.

---

## 1. Before starting an agent run

### 1.1 Confirm the intended mode

Determine whether the session is:

```text
A. analysis only
B. architecture proposal
C. task creation
D. code implementation
E. regression/review
F. documentation update
```

Do not let the agent write code during an analysis-only or proposal-only task.

### 1.2 Select the correct task file

Use a concrete task from:

```text
.agents/tasks/
```

Current expected backend execution sequence after BE-002/BE-003:

```text
TASK-BE-004-complex-backend-architecture-audit.md
TASK-BE-005-backend-contract-safety-net.md
```

Do not rerun old tasks blindly if their work has already been performed.

### 1.3 Provide minimal but sufficient context

Include:

```text
- root AGENTS.md
- .agents/agents.md
- selected task file
- relevant .agents/info docs
- relevant .agents/info/improvements AIP docs
- relevant .agents/skills docs
- previous walkthrough/report if task continues prior work
```

Avoid dumping all redundant context when the task has a narrow scope.

For large repomix files, instruct the agent to inspect targeted files first.

---

## 2. Standard launch prompt pattern

Use this structure when starting an agent task.

```text
You are working inside the WebSolutions / WS CODE project.

Read first:
- AGENTS.md
- .agents/agents.md
- .agents/METHOD.md
- .agents/RUNBOOK.md
- .agents/REVIEW-CHECKLIST.md
- [selected task file]
- [relevant info docs]
- [relevant skills]

Execute only the selected task.

Important:
- inspect before editing
- preserve public contracts
- do not perform forbidden changes
- report uncertainty
- run available checks
- create/update the expected report/walkthrough
```

For analysis-only tasks, add:

```text
Do not modify production code unless the task explicitly requests it.
```

For implementation tasks, add:

```text
Make the smallest safe change that satisfies the task. Prefer tests before risky refactor.
```

---

## 3. Recommended run order by work type

### 3.1 Backend architecture audit

Use when the goal is to understand weaknesses before creating or modifying the next implementation task.

Read:

```text
.agents/tasks/TASK-BE-004-complex-backend-architecture-audit.md
.agents/info/BE-01-routing-and-scope.md
.agents/info/BE-02-blocks-read-side-flow.md
.agents/info/BE-06-eav-content-resolver.md
.agents/info/BE-07-block-attach-map.md
.agents/info/BE-08-content-seed-pipeline.md
.agents/info/improvements/AIP-BE-001-read-side-refactor.md
.agents/info/improvements/AIP-BE-002-category-response-contract.md
.agents/info/improvements/AIP-BE-003-resource-to-assembler-boundary.md
.agents/skills/ws_agent_regression_protocol.md
```

Expected output:

```text
.agents/reports/AUDIT-BE-004-backend-architecture-audit.md
```

The audit may recommend changes to the next task, but should not perform broad production refactors.

### 3.2 Backend contract safety net

Use when the goal is to create tests and harden boundaries before the next refactor.

Read:

```text
.agents/tasks/TASK-BE-005-backend-contract-safety-net.md
.agents/skills/ws_backend_contract_preservation.md
.agents/skills/ws_backend_eav_mapping.md
.agents/skills/ws_frontend_backend_contract.md
.agents/skills/ws_agent_regression_protocol.md
```

Expected work:

```text
- feature/contract tests for category endpoint
- unit tests for EavContentResolver and BlockAttachMap
- route registration test if applicable
- minimal fixtures/factories/helpers if needed
- optional tiny controller boundary cleanup only after tests
```

### 3.3 Backend read-side refactor

Use only after safety net exists or a task explicitly allows proceeding.

Read:

```text
AIP-BE-001-read-side-refactor.md
AIP-BE-002-category-response-contract.md
AIP-BE-003-resource-to-assembler-boundary.md
ws_backend_read_side_refactor.md
ws_backend_resource_boundary.md
ws_backend_repository_and_query_loading.md
ws_backend_assembler_decision.md
```

Expected posture:

```text
small bounded refactor
contract tests first
no endpoint shape change
no DB schema change
```

### 3.4 Seed/import pipeline work

Use only when working on content seeding/import.

Read:

```text
BE-08-content-seed-pipeline.md
AIP-BE-004-seeders-import-pipeline.md
ws_backend_seed_pipeline.md
```

Never mix seed/import refactor with API read-side refactor unless a task explicitly requires it.

### 3.5 Frontend/backend bridge work

Use when frontend breakage or response mismatch is involved.

Read:

```text
FE-ARCHITECTURE-SUMMARY.md
FE-BACKEND-CONTRACT-BRIDGE.md
ws_frontend_backend_contract.md
```

Rules:

```text
- do not scatter response-shape fixes through many components
- preserve response.data.data expectation
- respect View/index/presentation/UI primitive layering
```

---

## 4. During the agent run

### 4.1 Watch for scope drift

Stop or redirect if the agent starts doing unrelated work such as:

```text
- renaming public keys not requested by task
- changing frontend during backend task
- changing database schema without approval
- installing dependencies without permission
- deleting legacy files without proof
- rewriting entire subsystems
```

### 4.2 Require visible reasoning artifacts

For complex tasks, the agent should produce or update:

```text
- walkthrough.md
- audit report
- implementation notes
- regression results
- task modification proposal
```

The artifact should be specific enough for a human maintainer to review without reverse-engineering the agent's hidden process.

### 4.3 Require contract awareness

For backend category/read-side work, the agent must explicitly mention whether these remained compatible:

```text
response.data.data
content
sections
subcategories
blocks
children
childs
section
properties
metadata
priority
```

For frontend bridge work, the agent must mention whether these remained compatible:

```text
/en/services route
/en/blocks/categories/services API call
blockStore / navigationStore expectations
presentation component props
```

---

## 5. Validation commands

Use commands available in the project environment.

### 5.1 Backend

```bash
php artisan test
./vendor/bin/pest
./vendor/bin/pint --test
php -l app/Path/ChangedFile.php
php artisan route:list
```

If database is required but unavailable, the agent must state that runtime DB validation was not possible and provide static/manual regression notes.

### 5.2 Frontend

```bash
npm run test:run
npm run build
```

### 5.3 Manual API checks

When backend server is available:

```bash
curl -s http://localhost/api/en/blocks/categories/services | jq '.data | keys'
curl -s http://localhost/api/en/blocks/categories/services | jq '.data.subcategories[0]'
```

Adapt base URL to local environment.

---

## 6. Review procedure after an agent run

### 6.1 Read the report first

Before inspecting code, read the agent's final report/walkthrough.

Check whether it includes:

```text
- files inspected
- files changed
- tests/checks run
- contract preservation notes
- intentionally skipped work
- risks and next steps
```

### 6.2 Inspect changed files by layer

Recommended order:

```text
1. routes / controllers
2. repositories / query classes
3. support helpers / assemblers
4. resources
5. models / relations
6. seeders/import helpers
7. tests
8. docs/reports
```

### 6.3 Compare against task scope

Ask:

```text
- Did the agent do what the task asked?
- Did it avoid forbidden changes?
- Did it preserve public contract?
- Did it create new hidden coupling?
- Did tests cover the risky areas?
```

Use `.agents/REVIEW-CHECKLIST.md` for detailed review.

---

## 7. When to reject or rollback

Reject or rollback if:

```text
- endpoint response shape changed silently
- legacy keys were removed without migration
- tests were added but do not assert meaningful contracts
- Resource now performs SQL
- Controller gained more business/query logic
- a helper became a God object
- seed content changed unexpectedly
- frontend was changed to compensate for backend instability without approval
- the final report overclaims validation
```

Partial acceptance is allowed: keep tests/docs, reject risky production changes.

---

## 8. How to modify a task after audit

If an audit task recommends modifying the next task:

```text
1. Preserve the original intent.
2. Add audit findings as context.
3. Re-rank priorities by risk.
4. Convert broad ideas into concrete allowed/forbidden changes.
5. Add regression expectations.
6. Mark postponed items explicitly.
```

Do not silently replace one task with a different one.

Use a report section:

```text
Recommended modification of next task
```

---

## 9. Handling failed or incomplete runs

If the agent cannot complete a task:

```text
- keep useful analysis
- state what was completed
- state what was not completed
- preserve partial reports
- do not claim success
- create a narrower follow-up task if needed
```

Failed runs can still produce valuable audit material.

---

## 10. Recommended cadence for backend execution

Current recommended execution path:

```text
1. Run TASK-BE-004 audit.
2. Review audit report manually.
3. Adjust TASK-BE-005 if audit findings require it.
4. Run TASK-BE-005 safety net.
5. Review tests and contract assertions.
6. Only then plan the next implementation/refactor task.
```

Avoid:

```text
TASK-BE-002 → TASK-BE-003 → another broad refactor immediately
```

The next high-value step is stronger evidence, not more movement.

---

## 11. Final report format for agent runs

Recommended final report:

```text
## Summary

## Files inspected

## Files changed

## What was improved

## Public contracts preserved

## Tests/checks run

## Tests/checks not run

## Risks / remaining debt

## Recommended next step
```

For audit tasks, replace `Files changed` with:

```text
## Findings by priority
## Recommended task modifications
```

---

## 12. Operator notes

The human operator should treat agent output as an engineering proposal until reviewed.

Especially inspect:

```text
- migrations
- route files
- public response keys
- seeders
- EAV transformations
- frontend bridge assumptions
- tests that appear green but assert little
```

The goal is not to slow down development. The goal is to prevent agent speed from creating hidden architectural debt.
