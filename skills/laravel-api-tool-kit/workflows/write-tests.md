# Workflow: Write Tests

Use when adding tests for any feature. See `rules/testing.md` for principles — this is the step-by-step.

---

## Step 1 — Pick the Test Type

Use the decision table in `rules/testing.md#pick-the-test-type`. When in doubt: feature test.

---

## Step 2 — Name the File by Concern

Not `CarTest.php`. Ask: what behavior does this file cover?

| Concern | File name |
|---|---|
| CRUD operations | `CarCrudTest.php` |
| Permissions / visibility | `CarAudienceTest.php` |
| Input validation | `CreateCarValidationTest.php` |
| Notifications / events | `CarNotificationsTest.php` |

Multiple concerns → multiple files from the start. Never dump everything into one file.

---

## Step 3 — Write the Happy Path

Extend `BaseTestCase` (create it if missing — see `rules/testing.md#basetestcase-pattern`).

**If writing a unit test** (external service, invisible side effect, or pure logic) → see `rules/testing.md#writing-unit-tests` for the three patterns.

**If testing a job** → create an `executeJobDirectly()` helper first (see `rules/testing.md#testing-jobs`).

**If the feature uses Redis or cache state** → create infrastructure setup helpers alongside factories (see `rules/testing.md#infrastructure-state-redis-cache`).

**If the feature emits mail, notifications, or queued jobs** → assert the content via closures, not just that something was sent (see `rules/testing.md#asserting-side-effects`).

**Arrange → Act → Assert.** For writes: assert status AND DB. For reads: assert status AND JSON shape.

```php
test('user can create a car', function () {
    $user = $this->loginAsUser();

    $this->postJson('/api/cars', ['name' => 'Tesla', 'color' => 'red'])
        ->assertCreated();

    $this->assertDatabaseHas('cars', [
        'name'    => 'Tesla',
        'user_id' => $user->id,
    ]);
});
```

---

## Step 4 — Add Failure Paths

Every feature needs at least one failure test. Cover whichever apply:

- Unauthenticated → 401
- Invalid input → 422
- Business rule rejection (wrong owner, blocked user, etc.) → 403 / 409

```php
test('unauthenticated user cannot create a car', function () {
    $this->postJson('/api/cars', ['name' => 'Tesla'])->assertUnauthorized();
});
```

---

## Step 5 — Add Edge Cases (Only If the Feature Has Them)

Apply these only when the feature calls for them — do NOT add ceremonial tests for cases that don't exist.

| If the feature has... | Add a test for... | Pattern |
|---|---|---|
| Different behavior per role / relationship | Multi-actor in one test | `rules/testing.md#multi-actor-testing` |
| Behavior that changes when state changes mid-scenario | State transition | `rules/testing.md#state-transitions` |
| A threshold, limit, or configurable count | Boundary values (at, above, zero) | `rules/testing.md#boundary-values` |
| Optional configuration fields | Null/missing config fallback | `rules/testing.md#boundary-values` |
| Date, duration, or expiry logic | Time-frozen test with `travelTo()` | explicit absolute timestamps |
| Recurring/periodic logic (daily reset, cron, timezone) | Safe instant — pin away from boundary | `rules/testing.md#safe-instants-for-periodic-logic` |

---

## Step 6 — Extract Helpers, Then Stop

- Assertion repeated 2+ times → extract to `assertXxx()` with a failure message
- Complex domain with many test files sharing setup → extract to a trait, compose via `uses()`

Stop there. Do not over-engineer the test helpers.

---

## Before Finishing

- [ ] Happy path asserts status + DB (writes) or JSON shape (reads)
- [ ] At least one failure path exists
- [ ] Edge cases added **only** if the feature has them
- [ ] Time frozen with `travelTo()` for any date logic
- [ ] Actions used as setup, not as test subjects
- [ ] No hardcoded IDs, no seeded data, no real external service calls
- [ ] `assertJsonPath` used instead of `assertSee`
- [ ] Repeated assertions extracted to named helpers
