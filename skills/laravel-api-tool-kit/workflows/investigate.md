# Workflow: Investigate & Debug

Structured debugging workflow for any issue — wrong response, missing data, queue failures, auth problems, performance issues. Works for any Laravel API project.

**Trigger phrases**: "investigate", "debug", "something is wrong with", "why is X not working", "track down", `/investigate`

---

## Core Principle: Data Before Code — ALWAYS

**NEVER start by reading code.** Code is complex and usually correct. Most issues are configuration or data problems.

Before touching any PHP file, query the database and check application state. Only read code if the data looks correct but the outcome is still wrong.

This rule exists because tracing code paths feels productive but wastes hours. A 30-second query eliminates a 2-hour code trace.

---

## Common Issue Patterns (Check These First)

Recognizing the pattern from the symptom lets you skip straight to the right data source. Match the symptom before opening any file:

| Pattern | Symptom | First Check |
|---|---|---|
| **Cache** | Data correct in DB but API returns stale/wrong value | Compare `Cache::get('KEY')` vs DB value |
| **Queue / async** | Action should have happened but didn't (email, notification, counter) | `failed_jobs` table, Horizon status, listener exceptions |
| **Timezone** | Wrong dates, records missing from date ranges, off-by-one on time queries | UTC in DB vs local time in request |
| **Field / type mismatch** | Query returns nothing unexpectedly, comparison always fails | `information_schema.columns` — check actual column type and name |
| **Business logic** | Feature worked before, stopped after deploy or data change | Read the requirement vs the actual code path |
| **Config / env** | Works locally, broken on staging/production | `config('key')` via tinker — env vars may not be loaded after `config:cache` |

The right pattern for your project depends on its architecture. If it uses heavy caching, cache bugs dominate. If it's queue-heavy, async issues dominate. Use the knowledge base (Step 0) to see what this project's common patterns actually are.

---

## Step 0 — Check Knowledge Base First

Before investigating anything, check if this is a known issue:

1. Look for the knowledge directory: `.claude/knowledge/` (Claude Code), `.agent/knowledge/` (Antigravity), or `knowledge/` (project root for Cursor/Copilot)
2. Find the file matching the feature area
3. Read "Issues Found" and "Key Lessons"

**If match**: Tell user what we know → verify with data → propose fix.
**If no match**: "No prior knowledge for this area. Investigating fresh."

---

## Step 1 — Understand the Problem (2 min max)

Get from the user:
- What happened vs what should have happened?
- Any IDs to start with? (model ID, user ID)
- When did it start? (after deploy, always, intermittent?)

Do NOT ask for error messages, logs, or curl responses before starting — just start investigating with whatever you have.

---

## Step 2 — Multi-Source Data Investigation

Check the data from **three sources** and compare them. Mismatches between sources reveal the bug:

### Source 1: Database (raw truth)

**How to query**: Use the best available method, in this priority order:

1. **MCP database tools** (preferred) — If MCP tools like `mcp__postgres__query`, `mcp__mysql__query`, or any database MCP server are available, use them directly. They give instant SQL access without needing a shell or tinker session.
2. **`php artisan tinker`** — Run Eloquent or raw DB queries via the shell.
3. **Ask the user** — If neither is available, provide the SQL and ask the user to run it.

> **Tip**: Check available tools at the start of investigation. If you see any MCP tool with "postgres", "mysql", "database", "db", or "sql" in the name — use it for all database queries throughout the investigation.

```sql
-- What does this record actually look like?
SELECT * FROM orders WHERE id = 'ORDER_ID';

-- Check column types (catches VARCHAR-vs-INT gotchas)
SELECT column_name, data_type FROM information_schema.columns
WHERE table_name = 'TABLE_NAME';

-- Recent records
SELECT * FROM orders WHERE user_id = 'USER_ID' ORDER BY created_at DESC LIMIT 10;

-- Count affected records
SELECT status, COUNT(*) FROM orders
WHERE created_at > NOW() - INTERVAL '24 hours' GROUP BY status;

-- Missing relationships
SELECT o.id, o.user_id, u.id as user_exists
FROM orders o LEFT JOIN users u ON u.id = o.user_id
WHERE o.id = 'ORDER_ID';
```

### Source 2: API (what users see)
```bash
# Test the endpoint directly
curl -s -X GET "http://localhost/api/orders/ORDER_ID" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json" | jq .
```

Compare API response with DB data. If they differ → cache issue, resource transformation bug, or middleware problem.

### Source 3: Application State (Tinker / MCP)

For raw model state, casts, accessors, and relationships — use tinker. If MCP database tools are available, you can also run raw SQL here to cross-check Eloquent behavior vs raw DB values (this comparison itself catches cast/accessor bugs).

```bash
# Check model state (includes casts, accessors, relationships)
php artisan tinker --execute="echo json_encode(App\Models\Order::find('ORDER_ID')->toArray(), JSON_PRETTY_PRINT);"

# Check cache
php artisan tinker --execute="echo Cache::get('CACHE_KEY');"

# Check config
php artisan tinker --execute="echo json_encode(config('services.stripe'), JSON_PRETTY_PRINT);"

# Test a service method directly
php artisan tinker --execute="echo app(App\Services\OrderService::class)->calculateTotal('ORDER_ID');"
```

### Where the Mismatch Tells You the Bug

| DB vs API | DB vs Tinker | Diagnosis |
|---|---|---|
| DB correct, API wrong | DB correct, Tinker correct | Resource transformation or cache issue |
| DB correct, API wrong | DB correct, Tinker wrong | Model accessor/cast or scope problem |
| DB wrong | - | Data was written incorrectly — trace the write path |
| DB correct, API correct | - | Issue is elsewhere (frontend, auth, timing) |

---

## Step 3 — Check Application Logs & Queue State

If data looks correct across all sources, check the application layer:

### Logs
```bash
# Recent errors
tail -n 200 storage/logs/laravel.log | grep -i "error\|exception\|failed"

# Feature-specific
grep -i "order\|payment\|FEATURE" storage/logs/laravel.log | tail -50
```

### Queue / Jobs

Use MCP database tools for these queries if available — they're faster than tinker for SQL:

```sql
-- Stuck jobs
SELECT id, queue, payload->>'displayName' as job, attempts, created_at
FROM jobs ORDER BY created_at DESC LIMIT 20;

-- Failed jobs
SELECT id, queue, payload->>'displayName' as job, exception, failed_at
FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
```

```bash
# Horizon status
php artisan horizon:status
php artisan queue:failed
```

### Cache
```bash
# Is stale cache the problem?
php artisan tinker --execute="
  \$cached = Cache::get('KEY');
  \$fresh  = Model::find('ID');
  echo 'Cached: ' . json_encode(\$cached) . PHP_EOL;
  echo 'Fresh:  ' . json_encode(\$fresh) . PHP_EOL;
"
```

---

## Step 4 — Hypothesis Checkpoint (after 15 min)

**STOP.** Ask yourself:

1. **Am I tracing code or querying data?** If tracing code for 15+ min without a lead → STOP, go back to data.
2. **Can I state my hypothesis in one sentence?** If not → write it down and test with one specific query.
3. **Code bug or config/data issue?** Most issues are config. Have I verified configuration?
4. **Would a query DISPROVE my theory?** Run it. If you can't disprove it, you haven't tested it.

---

## Step 5 — Read Code (only if data looks correct)

Only open PHP files if:
- All three data sources agree but the outcome is still wrong
- You need to understand HOW a value is computed
- The issue is clearly logic, not data/config

When reading code, focus on:
- The specific function that processes the queried data
- Input → output path only — not the entire call chain

**Common Laravel logic traps:**
- Enum comparison using raw string instead of enum case
- `auth()->id()` in a queued job (always null in queue)
- `$request->all()` instead of `$request->validated()`
- Missing `whenLoaded()` causing silent N+1
- Event dispatched inside `DB::transaction()` with queued listener
- `->get()` loading entire table instead of pagination
- Timezone mismatch: UTC in DB but user sends local time
- `->format()` returns server timezone, not user timezone
- Cache key collides with another feature (check `CacheKeys` class)

---

## Step 6 — Confirm Root Cause with Data

**NEVER declare root cause without data proof.** Show:
- "These N records are affected and X% match the pattern"
- "This config value is Y but should be Z"
- "Cache returns X but DB returns Y — stale by N minutes"
- "This query returns empty when it should return results"

If you can't prove it with a query or test → you haven't found it yet.

---

## Step 7 — Challenge Your Own Analysis

Before presenting, spend 5 minutes attacking your conclusion:
1. Are there records that DON'T fit this pattern? Why?
2. If this is the cause, why did it work before? What changed?
3. Is there another explanation that fits the same data?

---

## Step 8 — Update Knowledge (always)

After confirming root cause:
1. Open (or create) the knowledge file for this feature (`.claude/knowledge/[feature].md`, `.agent/knowledge/[feature].md`, or `knowledge/[feature].md`)
2. Add the issue under "Issues Found & Fixed"
3. Add useful diagnostic queries
4. Add lessons under "Key Lessons" or "DB Gotchas"

**One file per feature** — never one file per ticket.

---

## Step 9 — Present to User

Structure:
1. **What I found** — data evidence (queries, comparisons)
2. **Why it happens** — root cause in plain language
3. **How to fix** — config change or code change with example
4. **Confidence** — what % of affected cases match
5. **Prevention** — how to avoid this in the future (test, validation, monitoring)

---

## Step 10 — Should This Become a Workflow?

After finishing, ask: "Will someone hit this type of issue again?"

If yes — run `/create-workflow` to capture this investigation as a specialized workflow (e.g., `workflows/cache-investigator.md`, `workflows/queue-debugger.md`).

This is how `server-investigator.md` and `firebase-investigator.md` were born — from real investigations that turned into reusable workflows.

---

## Anti-Patterns

❌ Reading code depth-first before querying data
❌ Spending 30+ min on one hypothesis without testing it
❌ Asking for logs, curl responses, ticket numbers before starting — just start
❌ Reading 5+ files trying to "understand the flow"
❌ Declaring root cause without data proof
❌ Assuming code bug when config is more likely
❌ Creating standalone investigation docs — use knowledge files
❌ Finishing an investigation without updating knowledge

✅ Query data first, every time
✅ Compare DB vs API vs Tinker — mismatches reveal the bug
✅ State hypothesis in one sentence, test with one query
✅ Checkpoint at 15 minutes
✅ Prove root cause with numbers
✅ Challenge findings before presenting
✅ Update knowledge so the next investigation starts ahead
✅ Create a specialized workflow if the pattern will recur
