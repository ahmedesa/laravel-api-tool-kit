# Workflow: Code Review

A defense-oriented multi-phase code review. Validates feature completeness against acceptance criteria, catches real bugs (not just style), and separates introduced issues from pre-existing debt.

**Trigger phrases**: "review this code", "code review", "check this PR", "review this branch", `/code-review`

---

## Phase 1 — Gather Context

Collect ALL context before reading a single line of code:

### 1a. Understand the ticket
- Extract every acceptance criterion as a testable checklist item
- Note what should NOT happen (negative criteria are easy to miss)

### 1b. Get the full diff — never filter early
```bash
# Find the merge base
git merge-base <branch> origin/master

# See exactly which files changed
git diff <base>...<branch> --stat

# Read every change — no file filters
git diff <base>...<branch>

# Understand commit history
git log <base>...<branch> --oneline
```

Read EVERY changed file including constants, configs, migrations, and small utility files. A change to a constants array has more behavioral impact than most new classes.

### 1c. Check project standards
Read `SKILL.md` → Project Defaults. These are mandatory constraints — violations are findings.

---

## Phase 2 — Acceptance Criteria Validation

For EACH acceptance criterion:
1. Locate the code that implements it
2. Trace the logic end-to-end (controller → action → service → repo → DB)
3. Verify edge cases in the AC are handled
4. Confirm a test covers it

Output table:

| Criterion | Status | Evidence |
|---|---|---|
| [criterion text] | PASS / FAIL / VERIFY | [file:line or explanation] |

If a criterion says "X should NOT happen" — find the explicit code preventing it. Absence is not protection.

---

## Phase 3 — Structural Review

Work through each section. Flag every violation.

### 3.1 File Basics (Every File)
- [ ] `declare(strict_types=1)` present
- [ ] Every method has return type and parameter types
- [ ] No `mixed` unless truly unavoidable
- [ ] No nested ternaries — `match` used instead
- [ ] No magic numbers or hardcoded business-meaning strings
- [ ] No commented-out code
- [ ] No `env()` calls outside `config/*.php`

### 3.2 Dependency Injection
- [ ] All constructor deps use `private readonly` promotion
- [ ] No services/actions/repos instantiated with `new`
- [ ] No `resolve()` or `app()` inside constructors (acceptable only in traits/static methods)
- [ ] No static calls on injectable classes

### 3.3 Controllers
- [ ] Extends base `Controller`
- [ ] No business logic in controller methods
- [ ] No query building in controller methods
- [ ] Uses `$request->validated()` — no `$request->all()` or `$request->except()`
- [ ] All messages use `trans()`
- [ ] All responses use `ApiResponse` trait methods
- [ ] All model data wrapped in a Resource
- [ ] `responseCreated()` for store, `responseDeleted()` for destroy
- [ ] Authorization uses `$this->authorize()`
- [ ] Sensitive routes have `throttle` middleware

### 3.4 Models
- [ ] `$fillable` explicitly defined — no `$guarded = []`
- [ ] `$casts` covers booleans, enums, arrays, dates
- [ ] `Filterable` trait and `$default_filters` bound for filterable models
- [ ] ULID models have `HasUlids` trait + `$keyType = 'string'` + `$incrementing = false`
- [ ] No external service calls in model methods
- [ ] Scopes accept optional `$userId` — no hardcoded `auth()->id()`
- [ ] No scope for simple single-column conditions

### 3.5 Filters
- [ ] Extends `QueryFilters`
- [ ] `$allowedFilters` and `$allowedSorts` populated
- [ ] Custom filter methods operate on `$this->builder`, return `void`
- [ ] No `auth()->id()` hardcoded
- [ ] Bound on model via `$default_filters`
- [ ] Controller does NOT hardcode `with()` for relationships listed in `$allowedIncludes`

### 3.6 Resources
- [ ] Every relationship uses `whenLoaded()`
- [ ] Conditional fields use `$this->when()` — no inline ternaries that null a field
- [ ] No `->exists()`, `->count()`, `->first()` inside `toArray()`
- [ ] All timestamps use `dateTimeFormat()`
- [ ] No business logic inside `toArray()`

### 3.7 Form Requests
- [ ] Separate Create and Update requests
- [ ] Update uses `sometimes` for optional fields
- [ ] `authorize()` returns `true` (auth in Policy, not here)
- [ ] Enum validation uses `Rule::in(Enum::values())`

### 3.8 Actions & Services
- [ ] Actions are `final readonly class`
- [ ] Single public `execute()` method
- [ ] No HTTP concerns (`$request`, `response()`, `redirect()`)
- [ ] No query/filter building inside Actions
- [ ] Services have Interface + Concrete + Mock + ServiceProvider
- [ ] Services throw exceptions — never return HTTP responses

### 3.9 DTOs
- [ ] `final readonly class` with `private` constructor promotion
- [ ] Getter methods — no public properties
- [ ] Immutable — no setters
- [ ] Used when params would be `array` or exceed 3

### 3.10 Events
- [ ] Event properties are `private readonly` with getter methods
- [ ] Listeners implement `ShouldQueue` for I/O
- [ ] Events dispatched AFTER main operation, OUTSIDE `DB::transaction()`

### 3.10b Jobs
- [ ] Implements `ShouldQueue`
- [ ] Defines `$tries`, `$backoff`, and `$timeout`
- [ ] No `auth()->id()`, `request()`, or HTTP-context helpers inside `handle()`
- [ ] Not dispatched inside `DB::transaction()`
- [ ] Idempotent — safe to retry without duplicating side effects
- [ ] Constructor receives only serializable data (primitives, model IDs, models via `SerializesModels`)
- [ ] Mutable state re-fetched in `handle()` — not trusted from serialized constructor data

### 3.11 Database
- [ ] New tables follow project's PK convention
- [ ] Multi-table writes wrapped in `DB::transaction()`
- [ ] `DB::table()` NOT used when an Eloquent model exists — raw query builder bypasses soft deletes, global scopes, and model events
- [ ] Bulk updates use `whereIn()->update()`
- [ ] Counter columns use `increment()`/`decrement()`

### 3.12 Constants — Watch for Array Modifications
If a constants array was modified (item added or removed):
- **This is a behavioral change affecting ALL consumers of that constant, not just new code**
- Trace every usage of the modified constant across the codebase
- Removing an item from a status/type array can silently disable paths other features depend on
- Explain why the change is safe for EACH consumer

### 3.13 Tests
- [ ] Feature test exists for every endpoint action
- [ ] Uses factories — no hardcoded IDs
- [ ] Uses `RefreshDatabase`
- [ ] Asserts response status AND database state for writes
- [ ] Asserts JSON structure for reads
- [ ] Tests at least one failure path (unauthenticated, invalid input, forbidden)
- [ ] Expected values are hardcoded in tests — not reusing implementation constants
- [ ] Time-dependent tests freeze time with `$this->travelTo()`
- [ ] If code has multiple execution modes (DB vs Redis, cached vs fresh) — tests cover both

---

## Phase 4 — Defense Review (Where Real Bugs Hide)

Switch to adversarial mode. For each piece of logic, ask:

### Data Integrity
- [ ] Can two concurrent requests corrupt this data? (race conditions, TOCTOU)
- [ ] If this fails midway, is partial data left behind? (`DB::transaction()` needed?)
- [ ] Can this produce duplicate records? (unique constraints, upsert?)
- [ ] Are counter columns updated atomically? (`increment()` vs read-modify-save)
- [ ] If a file/image is overwritten, is the old file cleaned up from storage? (orphaned files accumulate silently)

### Data Source Validation
**Do NOT treat method calls as black boxes.** When new code calls a method to retrieve records:
- Read the method and understand EXACTLY what records it returns
- Ask: "Does every record type this method returns qualify for this new use case?"
- A method named `getActiveOrders()` might include recently-completed orders, transitional states, or records within a grace window that make them stale for the new purpose
- Pay attention to time-window inclusions — "ARRIVED orders within last 30 minutes" may be operationally invalid for a feature that assumes truly active records
- Negative tests: verify that invalid/edge-status records do NOT trigger the new behavior

### Consistency Between Methods
- If a bug was fixed in one method, was the SAME fix applied to sibling methods?
- Are similar classes (same pattern) consistent with each other?
- Check base classes and traits before flagging a missing method — it may be inherited

### Query Correctness
- [ ] Will this query scale? (missing index on WHERE/ORDER BY columns)
- [ ] Is there an N+1 hidden behind a loop, collection map, or resource?
- [ ] Does `->get()` load the entire table when it should paginate/chunk?
- [ ] Are date comparisons timezone-aware? (UTC in DB, local in user input)
- [ ] `->pluck('key')` on a collection with duplicate keys silently drops records — use `->keyBy()` and validate cardinality
- [ ] Soft-deleted records handled in all queries that need to include/exclude them?

### Cache & State
- [ ] If cached data is stale, what breaks? How is it invalidated?
- [ ] Is `auth()->id()` used in a queued job? (always null in queue context)
- [ ] Are enum comparisons using the enum case, not a raw string?

### Serialization & Queues
- [ ] Models passed to queue jobs — will they still exist when the job runs?
- [ ] Can the listener fail and retry safely? (idempotent?)
- [ ] Does the event carry its own data, or does it rely on DB state that may change before the listener runs?

### Identifiers
- [ ] `uniqid()` is microsecond-based and collision-prone under concurrent requests — use `Str::uuid()` or `Str::ulid()` instead

### Docblocks vs Reality
- [ ] Do comments/docblocks match what the code actually does?
- [ ] Are "no DB hit" or "lightweight" claims actually true?

### Edge Cases
- [ ] What happens with empty input? (empty array, null, empty string)
- [ ] What if the related model is soft-deleted?
- [ ] Off-by-one: is `>=` correct vs `>`? Is the boundary tested?

---

## Phase 5 — Scope Discipline

Classify every finding before reporting:

| Finding | Introduced or Pre-existing? | Verdict |
|---|---|---|
| N+1 in UserResource | Pre-existing (file not changed) | Note for backlog, don't block |
| Missing `trans()` on new message | Introduced | Must fix |

**Rules:**
- Only block the PR for issues **introduced** by this change
- Pre-existing issues get logged but don't block merge — file separate tickets
- **"Existing pattern" defense**: if a file already uses a pattern (inline validation, etc.), the developer following that pattern is acceptable. Refactoring mid-feature is scope creep. EXCEPTION: if the developer rewrote the code (changed constructor, replaced logic), they must use the correct standard — "I was just swapping a dependency" is not a defense if they changed the signature

---

## Phase 6 — Final Report

### Summary
One paragraph: what the change does, overall quality assessment.

### Acceptance Criteria Table
The table from Phase 2.

### Findings Table

| # | Severity | File:Line | Finding | Action |
|---|---|---|---|---|
| 1 | CRITICAL | `CarController.php:45` | `$request->all()` used | Must fix |
| 2 | HIGH | `CarResource.php:12` | `$this->owner` without `whenLoaded()` | Must fix |
| 3 | MEDIUM | `CarFilters.php:8` | Missing `created_at` in `$allowedSorts` | Should fix |

### Severity Definitions

**CRITICAL (block merge) — real production risk only:**
- Production bugs, data loss, security issues, data integrity risks
- `$request->all()` used anywhere
- `response()->json()` used directly
- Business logic in controller
- N+1 via unguarded relationship in Resource
- Race condition or data corruption risk
- Missing `DB::transaction()` on multi-table write
- `auth()->id()` in queued job/listener
- Cross-domain joins or eager loading in Repositories/Models (DDD projects)
- `DB::table()` used when Eloquent model exists (bypasses soft deletes)
- Constants array modified with undocumented impact on consumers

**HIGH (must fix before merge):**
- Missing return types or parameter types
- Hardcoded user-facing strings (no `trans()`)
- `new ServiceClass()` instead of DI
- `->paginate()` instead of `->dynamicPaginate()`
- Missing test for new endpoint
- Event properties exposed as `public`
- `uniqid()` used for identifiers (collision-prone)
- `pluck()` with duplicate key risk causing data loss

**MEDIUM (fix or note for backlog):**
- Nested ternaries instead of `match`
- Missing `$casts` for booleans
- Enum values hardcoded in validation
- `->format()` instead of `dateTimeFormat()`
- Orphaned storage files (old image not deleted)
- Docblock doesn't match implementation

---

## Phase 7 — Practical Impact Assessment

Before rating severity, evaluate actual real-world impact:

### N+1 and Query Scale
- Count the **realistic cardinality**. A query-per-item in a loop of 1-4 items with indexed lookups is different from a loop of hundreds.
- Still flag the pattern if the fix is simple — the method may be called in a different context with higher cardinality later.
- For `Rule::exists()` on array inputs: Laravel runs one query per item. For small fixed-size arrays (< 10 items) this is acceptable. For variable-size arrays, use a single `whereIn()->count()` check.

### Validation Over-Engineering
- Validation should prevent invalid states, not re-implement runtime logic.
- If runtime code handles a "wrong but harmless" value gracefully, validation doesn't need to duplicate that check.
- Simple `exists:table,id` is usually sufficient. Reserve complex closures for cases where invalid data causes actual harm.

### Visibility of Failure
- Is the failure mode **visible** (500 error, failed job) or **silent** (wrong data silently saved)?
- Silent failures are always higher severity regardless of frequency.

### Reversibility
- Is it reversible (config change, cache flush) or permanent (data corrupted, files deleted)?

**Key principles:**
- Read every file. Do not summarize without reading.
- Be specific. Every finding must reference a file and line. Vague findings are worthless.
- No false positives. If you're unsure, investigate deeper before including the finding.
- Trace inheritance. Before flagging a missing trait or method, check if the base class provides it.
- Defend your findings. If you can't counter a developer's pushback, drop the finding — don't waste review cycles on noise.
