# [Feature Name] Knowledge

Last Updated: YYYY-MM-DD

## How It Works

Brief description of the feature's core flow — what it does, what the business purpose is, and the high-level path through the code.

## Architecture

### Database Tables
| Table | Purpose | Key Columns |
|---|---|---|
| `table_name` | What it stores | `column_a`, `column_b` |

### Key Files
| File | Responsibility |
|---|---|
| `app/Actions/DoThingAction.php` | Orchestrates the main operation |
| `app/Models/Thing.php` | Core model |
| `app/Filters/ThingFilters.php` | Query filtering |

### API Endpoints
| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/api/things` | List with filters |
| `POST` | `/api/things` | Create |

---

## Issues Found & Fixed

### Issue 1: [Short Descriptive Title]

**Symptom**: What the user reported or what was observed.

**Root cause**: What actually caused it — include data evidence.

**Fix**: What was changed (config, code, migration).

**Diagnostic query**:
```sql
SELECT ... FROM ... WHERE id = 'RECORD_ID';
```

**Lesson**: What to check first if this type of issue recurs.

---

## Diagnostic Queries

Reusable queries for investigating this feature. Always use placeholder variables.

```sql
-- [What this query reveals]
SELECT ... FROM ... WHERE user_id = 'USER_ID';
```

```sql
-- [Count affected records by status]
SELECT status, COUNT(*) FROM table_name
WHERE created_at > NOW() - INTERVAL '24 hours'
GROUP BY status;
```

---

## Caching Strategy

| Cache Key | TTL | When Cleared | What It Stores |
|---|---|---|---|
| `feature:USER_ID:data` | 15 min | On write | Serialized response |

**Common cache issue**: [Describe the most likely cache-related bug and how to verify]

```bash
php artisan tinker --execute="echo Cache::get('feature:USER_ID:data');"
```

---

## Key Lessons

- When [symptom X] → always check [Y] first
- [Non-obvious pattern] — [why it matters]
- [Gotcha] — [what breaks if you ignore it]

---

## DB Gotchas

- `table.column` is **VARCHAR** not INT — use `'value'` not `12`
- `table.column` stores JSON — cast in model `$casts`
- [Column/type mismatch] — [what breaks]

---

## Known Edge Cases

- [Edge case 1] — [what happens, how to handle]
- [Edge case 2] — [when it occurs]

---

## Related Features

- [Related feature] — [how they interact, shared tables/services]

---

## Change Log

| Date | Change | Context |
|---|---|---|
| YYYY-MM-DD | Initial creation | [What investigation prompted this] |
