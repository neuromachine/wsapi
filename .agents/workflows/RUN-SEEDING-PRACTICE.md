# RUN-SEEDING-PRACTICE — Practical Seed Data Workflow

You are working inside the WS Laravel backend repository.

## Goal

Continue after the preliminary backend refactor by preparing the project for practical content seeding of service packages and individual commercial offers.

This is not an architecture audit.
This is practical data/source/seeder work.

## Read first

```text
.agents/info/SEEDING-CONTEXT-MAP.md
.agents/contracts/CONTENT-FILE-FORMATS.md
.agents/contracts/SEEDER-EXECUTION-MAP.md
.agents/contracts/OFFERS-SEEDING-CONTRACT.md
```

## Execute in order

```text
.agents/tasks/TASK-SEED-001-service-offers-locale-expansion.md
.agents/tasks/TASK-SEED-002-ind-offers-category-and-package-normalization.md
```

## Hard constraints

Do not:

```text
- refactor API Resources
- refactor frontend
- change endpoint response shapes
- rename legacy keys like acticle/items/childs
- turn this into another test/framework task
- remove existing ru content
- delete old source files unless explicitly asked
```

You may:

```text
- modify seeders directly related to content import
- modify/add JSON content source files
- add small helper methods if they simplify locale support
- create reports documenting changed files and next data gaps
```

## Expected final reports

```text
.agents/reports/REPORT-SEED-001-service-offers-locale-expansion.md
.agents/reports/REPORT-SEED-002-ind-offers-category-and-package-normalization.md
```

## Suggested validation commands

Run if environment allows:

```bash
php artisan db:seed --class=ServicesBlockSeeder
php artisan db:seed --class=BlockForCpDataSeeder
php artisan test
php artisan route:list
```

If commands cannot run, provide manual/static validation notes. Do not block practical code/data changes solely because commands cannot run.

