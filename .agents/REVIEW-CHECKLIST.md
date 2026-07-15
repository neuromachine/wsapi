# REVIEW-CHECKLIST — WebSolutions Agent Output Review

> Purpose: provide a practical checklist for reviewing results produced by coding agents in the WebSolutions / WS CODE project.
>
> Use this document after every non-trivial agent run, especially backend read-side, seed/import, and frontend/backend contract tasks.

---

## 1. First-pass review

### 1.1 Report completeness

Check that the agent report includes:

```text
[ ] task name / task ID
[ ] short summary
[ ] files inspected
[ ] files changed
[ ] what was improved
[ ] what was intentionally not changed
[ ] contracts preserved
[ ] tests/checks run
[ ] checks not run and why
[ ] risks / remaining debt
[ ] recommended next step
```

Reject overconfident reports that do not include evidence.

### 1.2 Scope control

Check:

```text
[ ] Changes match the selected task.
[ ] No broad rewrite happened without approval.
[ ] No unrelated frontend/backend/seed files were touched.
[ ] No new dependency was installed without explicit task permission.
[ ] No database schema change happened unless explicitly allowed.
[ ] No public route changed silently.
```

---

## 2. Backend contract review

Use for Laravel API tasks.

### 2.1 API envelope

```text
[ ] Response still uses Laravel Resource envelope where expected.
[ ] Frontend payload remains available at response.data.data.
[ ] Error response shape did not change accidentally.
```

### 2.2 Category endpoint contract

For `GET /{locale}/blocks/categories/{slug}`, especially `/en/blocks/categories/services`:

```text
[ ] data.id exists.
[ ] data.key exists.
[ ] data.name exists.
[ ] data.description exists.
[ ] data.content exists.
[ ] data.parent_id exists.
[ ] data.created_at exists.
[ ] data.updated_at exists.
[ ] data.section exists.
[ ] data.sections exists.
[ ] data.subcategories exists.
[ ] data.blocks exists.
[ ] data.children exists.
```

Inside `subcategories`:

```text
[ ] id exists.
[ ] slug exists.
[ ] childs exists if previously present.
[ ] title exists when EAV data provides it.
[ ] descr exists when EAV data provides it.
[ ] content exists when EAV data provides it.
[ ] metadata exists when EAV data provides it.
[ ] priority exists when EAV data provides it.
```

### 2.3 Legacy compatibility

```text
[ ] childs was not removed casually.
[ ] acticle was not renamed casually.
[ ] items structure was not changed casually.
[ ] locale/scope/section behavior was not changed casually.
[ ] HTML content was not sanitized/modified unless task required it.
[ ] ru/en/vi data coverage was not reduced.
```

---

## 3. Backend architecture boundary review

### 3.1 Controller boundary

```text
[ ] Controller remains thin.
[ ] Controller does not contain new complex Eloquent queries.
[ ] Controller does not perform EAV transformation.
[ ] Controller does not assemble complex category payloads.
[ ] Route params are passed clearly to repository/service/use-case.
```

Existing debt may remain if task did not target it, but new controller debt should not be introduced.

### 3.2 Repository/query boundary

```text
[ ] Repository/query layer prepares data intentionally.
[ ] Eager loading is explicit and relevant.
[ ] Locale filtering remains correct.
[ ] Recursive/category loading is not accidentally weakened.
[ ] Repository does not format frontend JSON directly.
[ ] Repository did not become a God object.
```

### 3.3 Resource boundary

```text
[ ] Resource does not perform SQL.
[ ] Resource does not call Repository.
[ ] Resource does not compensate for missing relations by querying.
[ ] Resource serializes explicit fields where possible.
[ ] attributesToArray() usage is intentional or documented.
[ ] Resource preserves compatibility keys.
```

### 3.4 Assembler/PayloadBuilder boundary

If an assembler exists or was modified:

```text
[ ] Assembler performs deterministic mapping only.
[ ] Assembler receives prepared models/collections.
[ ] Assembler does not query DB.
[ ] Assembler has a narrow response target.
[ ] Assembler does not absorb unrelated endpoint logic.
[ ] Assembler behavior is covered by tests or contract checks.
```

### 3.5 EAV resolver boundary

```text
[ ] EavContentResolver remains pure transformation.
[ ] It does not know routes/controllers.
[ ] It does not query DB.
[ ] single / keyed / array modes remain compatible.
[ ] value_type casting remains compatible.
[ ] is_collection behavior remains compatible.
[ ] sort/priority behavior remains compatible or documented.
```

### 3.6 BlockAttachMap boundary

```text
[ ] BlockAttachMap remains compatibility policy.
[ ] attach destinations did not change accidentally.
[ ] singleton/keyed behavior did not change accidentally.
[ ] No DB-driven migration was introduced unless task allowed it.
```

---

## 4. Routing and locale/scope review

```text
[ ] API routes are not double-prefixed accidentally.
[ ] /api prefix behavior is understood and checked.
[ ] {locale} route prefix still accepts expected two-letter values.
[ ] SetLocale behavior remains compatible.
[ ] Blocks GET endpoints still use locale as data scope where expected.
[ ] Forms POST endpoint still uses locale for validation messages where expected.
[ ] Frontend scope and backend locale were not conflated further.
```

Check `php artisan route:list` when available.

---

## 5. Seeder/import review

Use for seed/import tasks.

### 5.1 Source preservation

```text
[ ] Actual content text was not changed unless requested.
[ ] Content keys were not renamed.
[ ] JSON file paths remain compatible.
[ ] Storage::disk('blocks') usage remains correct.
[ ] BlockContentHelper behavior remains compatible.
```

### 5.2 Database output preservation

```text
[ ] blocks are still created.
[ ] blocks_categories are still created.
[ ] block_items are still created.
[ ] block_item_properties are still created.
[ ] block_item_property_values are still created.
[ ] locale values remain present.
[ ] value_type is still assigned correctly.
[ ] is_collection values still produce expected API output.
```

### 5.3 Import helper review

If `ImportHelper` or similar was introduced/modified:

```text
[ ] Helper reduces duplication without hiding too much behavior.
[ ] Helper does not encode business rules that belong to a specific seeder.
[ ] Missing keys/files fail clearly.
[ ] updateOrInsert/upsert behavior remains idempotent.
[ ] hardcoded IDs were not changed blindly.
[ ] hardcoded keys were preserved unless safely resolved by key.
```

---

## 6. Frontend review

Use when frontend was touched.

### 6.1 Component layering

```text
[ ] View.vue remains composition-only.
[ ] index.vue owns orchestration/fetch/store usage.
[ ] presentation components receive props.
[ ] item components remain presentational.
[ ] UI primitives remain business-agnostic.
```

### 6.2 Store/composable behavior

```text
[ ] blockStore(id) factory behavior is preserved.
[ ] defineStore is not recreated per render for same ID.
[ ] usePageOrchestrator remains the fetch boundary.
[ ] isPageOwner behavior is not broken.
[ ] active fetch dedupe remains safe.
[ ] response.data.data is still used for Laravel Resource payload.
```

### 6.3 Scope and navigation

```text
[ ] AppLink scoped navigation remains compatible.
[ ] uiStore.scope remains URL prefix concept.
[ ] locale/i18n was not confused with backend scope.
[ ] navigation fetch remains non-blocking unless task changed it intentionally.
```

---

## 7. Test review

### 7.1 Backend tests

```text
[ ] Tests assert meaningful API contract, not only HTTP 200.
[ ] Tests include key JSON paths.
[ ] Tests cover reference endpoint or minimal equivalent fixture.
[ ] Tests cover EavContentResolver modes if resolver touched.
[ ] Tests cover BlockAttachMap if mapping touched.
[ ] Tests do not depend on fragile external state unless documented.
[ ] Factories/fixtures are minimal and readable.
```

### 7.2 Frontend tests

```text
[ ] Tests cover user-visible behavior or bridge contract.
[ ] Tests do not merely assert component mounts.
[ ] Tests include store/composable behavior if changed.
[ ] npm run test:run passes when available.
[ ] npm run build passes when available.
```

### 7.3 Static/style checks

```text
[ ] php artisan test or ./vendor/bin/pest was run, or reason given.
[ ] ./vendor/bin/pint --test was run, or reason given.
[ ] php -l was run for changed PHP files if full tests unavailable.
[ ] npm run build was run for frontend changes, or reason given.
```

---

## 8. Code quality review

### 8.1 SOLID / Clean Code orientation

```text
[ ] Single Responsibility is improved or preserved.
[ ] Open/Closed is not violated by hardcoding every future case into one method.
[ ] Liskov/Substitution concerns are not relevant or preserved.
[ ] Interface Segregation is not over-engineered.
[ ] Dependency Inversion is used pragmatically, not ceremonially.
```

### 8.2 Practical Laravel style

```text
[ ] Uses Laravel conventions where useful.
[ ] Does not fight the framework without reason.
[ ] Uses dependency injection where it improves testability.
[ ] Avoids service/repository layers that add no clarity.
[ ] Keeps Resources as transformation/serialization layer.
[ ] Keeps tests close to behavior.
```

### 8.3 Complexity check

```text
[ ] Net complexity decreased or risk decreased.
[ ] New abstractions are named after real responsibilities.
[ ] No new "Manager" / "Service" / "Helper" God object appeared.
[ ] Logic is discoverable from file names and method names.
[ ] Comments explain compatibility decisions, not obvious code.
```

---

## 9. Red flags

Stop and review deeply if you see:

```text
[ ] public JSON keys renamed
[ ] database schema changed unexpectedly
[ ] frontend changed during backend-only task
[ ] route URLs changed
[ ] locale/scope behavior changed
[ ] Resources contain Model::where or DB:: calls
[ ] Controllers contain large query closures
[ ] tests only assert status 200
[ ] report says "all tests pass" but no command output or command list exists
[ ] new package installed without approval
[ ] seed content changed unexpectedly
[ ] legacy fields deleted as "cleanup"
```

---

## 10. Acceptance decision

### Accept

```text
[ ] Scope matches task.
[ ] Contracts preserved.
[ ] Tests/checks are meaningful.
[ ] Risks are documented.
[ ] Code is easier or safer than before.
```

### Accept with follow-up

```text
[ ] Main goal achieved.
[ ] Some debt remains documented.
[ ] Follow-up task is clear and bounded.
```

### Partial accept

```text
[ ] Keep tests/docs.
[ ] Reject risky production changes.
[ ] Convert useful findings into a new task.
```

### Reject / rollback

```text
[ ] Public contract broken.
[ ] Scope drift is significant.
[ ] Validation is absent or misleading.
[ ] New architecture is more confusing.
[ ] Changes are too broad for review.
```

---

## 11. Reviewer final note template

Use this after review:

```text
Review result: Accepted / Accepted with follow-up / Partial accept / Rejected

Reason:

Contracts checked:

Tests checked:

Risks:

Required follow-up:
```

---

## 12. Core reminder

Agent output should accelerate engineering, not replace engineering judgment.

The best result is not the largest patch.

The best result is the safest next improvement that makes future work clearer.
