# Workflow: Code Review Checklist

Use this when reviewing any Laravel API code. Work through each section and flag every violation.

---

## 1. File Basics (Every File)

- [ ] Starts with `<?php` then blank line then `declare(strict_types=1);`
- [ ] Every method has a return type
- [ ] Every method parameter has a type declared
- [ ] No `mixed` used unless truly unavoidable
- [ ] No nested ternary operators — `match` used instead
- [ ] No magic numbers or hardcoded string values that have business meaning (use Constants)
- [ ] No commented-out code
- [ ] No `env()` calls outside of `config/*.php` files

---

## 2. Dependency Injection

- [ ] All constructor dependencies use `private readonly` property promotion
- [ ] No services or actions instantiated with `new` inside methods
- [ ] No `resolve()` or `app()` inside constructors
- [ ] No static calls on injectable classes

---

## 3. Controllers

- [ ] Extends the app's base `Controller`
- [ ] No business logic inside controller methods
- [ ] No query building inside controller methods
- [ ] Every method uses `$request->validated()` — no `$request->all()`, `$request->except()`
- [ ] All messages use `trans()` — no hardcoded strings
- [ ] All responses use `ApiResponse` trait methods — no `response()->json()`
- [ ] All model data wrapped in a Resource
- [ ] `responseCreated()` for store, `responseDeleted()` for destroy
- [ ] Authorization uses `$this->authorize()` — no inline auth checks
- [ ] Sensitive routes have `throttle` middleware

---

## 4. Models

- [ ] `$fillable` explicitly defined
- [ ] `$casts` covers all booleans, enums, arrays, dates
- [ ] `Filterable` trait and `$default_filters` bound for filterable models
- [ ] No external service calls in model methods (email, file upload, notification)
- [ ] No multi-model orchestration in model methods
- [ ] Scopes do NOT hardcode `auth()->id()` — accept optional `$userId`
- [ ] No scope created for a simple single-column condition — `where()` used directly

---

## 5. Filters

- [ ] Extends `QueryFilters`
- [ ] `$allowedFilters` and `$allowedSorts` populated
- [ ] Custom filter methods operate on `$this->builder` and return `void`
- [ ] No `auth()->id()` hardcoded inside filter methods
- [ ] Bound on the model via `$default_filters`

---

## 6. Resources

- [ ] Every relationship uses `whenLoaded()` — no unguarded relation access
- [ ] Conditional fields use `$this->when()` — no inline ternaries nulling a field
- [ ] No `->exists()`, `->count()`, or `->first()` inside `toArray()`
- [ ] All timestamps use `dateTimeFormat()`
- [ ] No business logic inside `toArray()`

---

## 7. Form Requests

- [ ] Separate Create and Update requests
- [ ] Update request uses `sometimes` for optional fields
- [ ] `authorize()` returns `true` (authorization in Policy, not here)
- [ ] Enum validation uses `Rule::in(Enum::values())` — not hardcoded strings

---

## 8. Actions & Services

- [ ] Actions are `final` classes
- [ ] Actions have a single public `execute()` method
- [ ] Services do not become God Objects (split if 10+ unrelated methods)
- [ ] No HTTP concerns (`$request`, `response()`, `redirect()`) in Actions/Services
- [ ] No query/filter building inside Actions/Services
- [ ] Injected via constructor — not `new` in calling code

---

## 9. DTOs

- [ ] Used when any parameter would be `array` OR total params exceed 3
- [ ] Declared as `final readonly class`
- [ ] Uses `private` constructor promotion (readonly implicit) + getter methods — no public properties
- [ ] No setters — DTOs are immutable
- [ ] Instantiated with `new` in the controller (allowed exception to DI)
- [ ] No business logic inside DTOs

---

## 10. Constants & Magic Numbers

- [ ] No unexplained numbers or strings in logic
- [ ] Cache / session / cookie keys use a `CacheKeys` class with `sprintf` — never inline strings
- [ ] Domain-wide constants in `app/Constants/` or Enums — not `app/Contracts`

---

## 11. Events

- [ ] Dedicated Event class — not a string event
- [ ] Event properties are `private` or `private readonly`
- [ ] Public getter methods exposed for each property
- [ ] Listeners implement `ShouldQueue` for any I/O work
- [ ] Events dispatched AFTER the main operation succeeds
- [ ] Not dispatched inside a DB transaction if listener is queued

---

## 12. Authorization

- [ ] Authorization logic in Policies — not inline in controllers
- [ ] `$this->authorize()` called on all write operations
- [ ] `authorize()` in FormRequests returns `true` — not doing real auth checks

---

## 13. Exception Handling

- [ ] Business rule violations use `UnprocessableEntityHttpException` — not `abort()`
- [ ] Single-model lookups use route model binding — not `find($id)` with manual 404
- [ ] No empty `catch` blocks
- [ ] Services and Actions throw exceptions — never return response objects

---

## 14. Enums

- [ ] PHP backed enum (string or int)
- [ ] `EnumHelpers` trait present
- [ ] Cast in Model's `$casts`
- [ ] Validation uses `Rule::in(Enum::values())`
- [ ] Comparisons use enum case — not raw string

---

## 15. Database

- [ ] New tables follow the project's primary key convention (check SKILL.md Project Defaults)
- [ ] Multi-table writes wrapped in `DB::transaction()`
- [ ] Relationships eager-loaded before loops and pagination
- [ ] Bulk updates use `whereIn()->update()` — not per-record saves
- [ ] Counter columns use `increment()` / `decrement()`

---

## 16. Pagination

- [ ] List endpoints use `->dynamicPaginate()` — not `->paginate()` or `->get()`
- [ ] `useFilters()` called before `dynamicPaginate()` for filterable models

---

## 17. Tests

- [ ] Feature test exists for every endpoint action
- [ ] Uses factories — no hardcoded IDs or manual model construction
- [ ] Uses `RefreshDatabase`
- [ ] Asserts both response status AND database state for writes
- [ ] Tests at least one failure path (unauthenticated, invalid input, forbidden)

---

## Violation Severity

**Critical (block merge):**
- `$request->all()` used anywhere
- `response()->json()` used directly
- Raw DB queries inside a Resource's `toArray()`
- Business logic in a controller
- Unguarded relationship access in Resources (N+1)
- Services returning response objects instead of throwing
- Missing `declare(strict_types=1)`

**High (must fix before merge):**
- Missing return types or parameter types
- Hardcoded user-facing strings (no `trans()`)
- Inline authorization instead of Policy
- `->paginate()` instead of `->dynamicPaginate()`
- `new ServiceClass()` instead of constructor injection
- Event properties exposed as `public`

**Medium (fix or leave note):**
- Nested ternaries instead of `match`
- `auth()->id()` hardcoded in model scope
- Enum values hardcoded in validation rules
- Missing `$casts` for booleans
- `->format()` on timestamps instead of `dateTimeFormat()`
- Missing feature test
