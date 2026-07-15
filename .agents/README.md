# .agents README — WebSolutions Agent Context Layer

## Purpose

The `.agents` directory contains project-specific context and execution materials for code agents such as Codex, Antigravity, Gemini-based tools, and similar systems.

This directory is not application runtime code. It is a structured context layer for safe architectural reasoning and refactoring.

---

## Directory Structure

Expected structure:

```text
.agents/
  README.md
  agents.md
  info/
    INDEX.md
    MATERIALS-MAP.md
    GLOSSARY.md
    BE-*.md
    FE-*.md
    improvements/
      AIP-*.md
  skills/
    *.md
  tasks/
    *.md
    templates/
      *.md
```

Not all folders must exist at all times.

---

## File Roles

### `README.md`

Human-facing explanation of the `.agents` directory.

Use it to understand:

- what the directory is for
- what each group of files means
- how to launch agent runs
- how to review results

---

### `agents.md`

Machine-oriented project instruction layer.

Agents should read it after root `AGENTS.md`.

It defines:

- execution protocol
- context priority
- compatibility-first rules
- refactoring behavior
- reporting expectations

---

### `info/`

Architecture documentation and project methodology.

This folder describes the system as it exists and how to reason about it.

Important files include:

```text
INDEX.md
MATERIALS-MAP.md
GLOSSARY.md
BE-00-overview.md
BE-01-routing-and-scope.md
BE-02-blocks-read-side-flow.md
BE-03-eav-domain.md
BE-04-repository-layer.md
BE-05-resource-layer.md
BE-06-eav-content-resolver.md
BE-07-block-attach-map.md
BE-08-content-seed-pipeline.md
FE-ARCHITECTURE-SUMMARY.md
FE-BACKEND-CONTRACT-BRIDGE.md
```

---

### `info/improvements/`

Architecture Improvement Proposals.

AIP documents describe possible future improvements, risks, options, and staged strategies.

They are not executable tasks.

Use them when a task touches planned refactor directions such as:

```text
- backend read-side refactor
- category response contract
- Resource / Assembler boundary
- seed import pipeline
- naming compatibility layer
- future Server-Driven UI
```

---

### `skills/`

Reusable agent instructions for recurring work patterns.

Skills should be short and focused. They should not duplicate full architecture docs.

A skill may describe how to approach:

```text
- backend read-side refactor
- contract preservation
- Resource boundary
- EAV mapping
- frontend/backend bridge
- manual regression
```

---

### `tasks/`

Task files contain explicit execution instructions.

A task may instruct an agent to inspect, refactor, validate, or produce a report.

A task is the only layer that should directly ask the agent to change code.

---

## Context Priority for Human Operators

For architecture understanding:

```text
1. .agents/info/INDEX.md
2. .agents/info/MATERIALS-MAP.md
3. .agents/info/GLOSSARY.md
4. relevant BE-* or FE-* docs
5. relevant AIP docs
```

For running agents:

```text
1. AGENTS.md
2. .agents/agents.md
3. selected task file
4. relevant info and skill files referenced by the task
```

---

## Legacy Context

The project may contain `.gemini/` or other legacy instruction folders.

Current rule:

```text
.agents/* is primary.
.gemini/ is secondary legacy context.
```

If `.gemini/` conflicts with `.agents/*`, follow `.agents/*`.

---

## Recommended Agent Launch Pattern

Use this pattern when launching a task:

```text
Use project agent instructions.
Read AGENTS.md and .agents/agents.md.
Execute: .agents/tasks/<TASK-FILE>.md
Treat .gemini/ as secondary legacy context.
Preserve existing public API and frontend contracts unless the task explicitly allows a breaking change.
```

For analysis-only tasks:

```text
Use project agent instructions.
Read .agents/info/INDEX.md and relevant architecture documents.
Do not edit source code.
Produce an analysis report only.
```

---

## Review Principles

When reviewing an agent result, check:

```text
- Did the agent read the right context?
- Did it inspect actual source code?
- Did it preserve public contracts?
- Did it avoid broad unrelated rewrites?
- Did it distinguish assumptions from facts?
- Did it run validation or provide manual regression notes?
- Is the final system easier to understand?
```

Do not accept a result solely because the report says it succeeded.

Review the actual diff.

---

## Project Refactoring Philosophy

The project is evolving from working organic code toward clearer architecture.

The goal is not theoretical purity.

The goal is:

```text
same behavior
clearer boundaries
less hidden logic
safer future extension
better documentation
```

Core rule:

```text
Improve without breaking.
```
