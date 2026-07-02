# Stage 11 — BE-005 Adjusted After BE-004 Audit

This package adds the next executable backend task after the completed `TASK-BE-004` architecture audit.

It assumes that Stages 1–9 and `TASK-BE-004` materials are already present in the backend repository.

## Contents

```text
.agents/
  tasks/
    TASK-BE-005-backend-contract-safety-net.md
    LAUNCH-BE-005-backend-contract-safety-net.md
    TASK-BE-006-route-api-bootstrap-cleanup.md
    TASK-BE-007-offers-endpoint-boundary-refactor.md
    TASK-BE-008-explicit-resource-serialization-hardening.md

  reports/
    templates/
      REPORT-BE-005-backend-contract-safety-net.template.md

  workflows/
    RUN-BE-005.md
```

## How to use

1. Copy this package into the backend repository root.
2. Run only `TASK-BE-005` first.
3. Do not run `TASK-BE-006/007/008` until BE-005 is completed and reviewed.

## Intended execution order

```text
TASK-BE-004  -> completed audit report
TASK-BE-005  -> contract safety net / tests
TASK-BE-006  -> route/bootstrap cleanup
TASK-BE-007  -> offers endpoint boundary refactor
TASK-BE-008  -> explicit Resource serialization hardening
```

## Important

`TASK-BE-005` is not a structural refactor. It should create a safety net before code architecture changes continue.

