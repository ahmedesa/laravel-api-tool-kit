# Workflow: Test an API Endpoint with Curl

Use during investigation or feature verification to test an endpoint directly and cross-reference its response against the database.

**Trigger phrases**: "test this endpoint", "curl this", "test the API", "verify the response", `/curl-test`

**Best used alongside `/investigate`** — curl response + DB comparison is the fastest way to prove a cache, resource, or transformation bug.

---

## Step 1 — Build the Curl Command

Generate the correct command based on the HTTP method:

```bash
# GET
curl -s -X GET "http://localhost/api/v1/ENDPOINT" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json" | jq .

# POST
curl -s -X POST "http://localhost/api/v1/ENDPOINT" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"key": "value"}' | jq .

# PUT / PATCH
curl -s -X PUT "http://localhost/api/v1/ENDPOINT/ID" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"key": "value"}' | jq .

# DELETE
curl -s -X DELETE "http://localhost/api/v1/ENDPOINT/ID" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"
```

**Always use `-s` (silent) + `| jq .` for readable output.** Add `-v` when you need to inspect response headers.

**If the user doesn't have a token**, provide the login command:
```bash
curl -s -X POST "http://localhost/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}' | jq .token
```

---

## Step 2 — Parse the Response

When the user shares the response, check:

### HTTP Status
| Status | Meaning | Next step |
|---|---|---|
| 200 / 201 | Success | Validate response structure |
| 401 | Unauthenticated | Check token / auth middleware |
| 403 | Forbidden | Check policy |
| 404 | Not found | Check route / model binding |
| 422 | Validation failed | Read `errors` field |
| 500 | Server error | Check `storage/logs/laravel.log` |

### Response Structure
- All expected fields present?
- No unexpected nulls?
- Correct data types (string vs number)?
- Timestamps in correct format and timezone?

---

## Step 3 — Cross-Reference with Database

This is the most valuable step. Compare the API response against raw DB data:

```sql
-- Get the raw record
SELECT * FROM table_name WHERE id = 'RECORD_ID';

-- If values differ → cache issue, resource bug, or accessor/cast bug
```

**Mismatch diagnosis:**

| API shows | DB shows | Likely cause |
|---|---|---|
| Stale value | Correct value | Cache not invalidated |
| Transformed value | Raw value | Accessor or cast applied |
| Missing field | Field exists | Resource not including it / `when()` condition false |
| Wrong value | Same wrong value | Data written incorrectly — trace the write path |

---

## Step 4 — Report Findings

```
Endpoint: GET /api/v1/cars/CAR_ID
Status: 200 OK ✓

Response: { "balance": 50 }
Database: balance = 100

⚠ Mismatch — API returns 50, DB has 100.
Likely cause: cached value not invalidated on last update.
Next: check Cache::get() for key 'car:CAR_ID:balance'
```

---

## Anti-Patterns

❌ Assuming the endpoint URL — check routes first (`php artisan route:list | grep keyword`)
❌ Skipping the DB comparison — response vs DB is the fastest bug locator
❌ Testing only the happy path — test unauthenticated, forbidden, invalid input too
❌ Hardcoding specific IDs in saved commands — use `CAR_ID`, `USER_ID` placeholders
