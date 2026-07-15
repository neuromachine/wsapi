# TEMPLATE — Architecture Proposal Task

## Status

Task template.

Use this template when the expected output is an architectural proposal, not code changes.

This is useful before creating AIP documents, deciding between refactor strategies, or evaluating a major system direction.

---

## Core Principle

Separate thinking from implementation.

The agent must analyze, compare options, and recommend a direction. It must not modify code unless the task explicitly adds an implementation phase.

---

## Task Name

`<TASK-ID> — <Architecture proposal title>`

---

## Context

Describe the architectural area:

```text
<module/system/layer>
```

Describe the pain or uncertainty:

```text
<problem to reason about>
```

Describe current known constraints:

```text
<constraint-1>
<constraint-2>
<constraint-3>
```

---

## Goal

Produce a proposal for:

```text
<architecture decision / refactor path / migration strategy>
```

The output should support a later decision or task creation.

---

## Required Context Intake

Inspect relevant docs and code:

```text
.agents/info/INDEX.md
.agents/info/MATERIALS-MAP.md
.agents/info/GLOSSARY.md
.agents/info/improvements/<relevant-AIP>.md
<relevant project files>
```

If no AIP exists yet, propose one.

---

## Required Analysis

The agent must cover:

```text
1. Current system behavior.
2. Current pain points.
3. Constraints and compatibility requirements.
4. At least two viable options.
5. Risks and trade-offs for each option.
6. Recommended direction.
7. What should not be changed yet.
8. Suggested next task if accepted.
```

---

## Option Format

Each option should include:

```text
- Summary
- What changes
- What remains compatible
- Benefits
- Risks
- Complexity
- When to choose it
- When not to choose it
```

---

## Decision Criteria

Use criteria such as:

```text
- compatibility risk
- implementation complexity
- long-term maintainability
- ability to migrate gradually
- clarity for future agents/developers
- amount of code touched
- ability to verify safely
```

---

## Forbidden Behavior

Do not:

```text
- edit code
- create migrations
- rename contracts
- present assumptions as facts
- recommend a full rewrite without strong justification
- ignore current working behavior
- hide uncertainty
```

---

## Expected Deliverable

The final response must include:

```text
1. Current state summary.
2. Problem statement.
3. Options compared.
4. Recommended direction.
5. Risks.
6. Open questions.
7. Suggested next AIP or task.
```

---

## Optional AIP Draft Output

If requested, structure the result as an Architecture Improvement Proposal:

```text
# AIP-<AREA>-<NUMBER> — <Title>

## Status
Draft

## Problem
...

## Current State
...

## Options
...

## Recommendation
...

## Compatibility Notes
...

## Migration Path
...

## Open Questions
...
```

---

## Success Criteria

The task succeeds if:

```text
- the proposal clarifies choices
- trade-offs are explicit
- compatibility is considered
- no code is changed
- the next step is actionable
```

---

## Failure Criteria

The task fails if:

```text
- it jumps directly to implementation
- it assumes user needs without evidence
- it ignores constraints
- it presents only one option without trade-off analysis
```
