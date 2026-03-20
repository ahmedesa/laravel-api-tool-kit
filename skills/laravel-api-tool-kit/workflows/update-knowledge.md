# Workflow: Update Knowledge Base

Run this after completing a feature, fixing a bug, discovering a non-obvious data pattern, or making an architectural decision. Preserves learnings so future sessions start where this one ended.

**Trigger phrases**: "update knowledge", "update the skill", "remember this", "save what we learned", "update skills"

**Also trigger proactively** — if a significant discovery was confirmed during the session (root cause found, non-obvious gotcha, reusable pattern), run this without being asked.

---

## Step 1 — Scan the Conversation

Review the full conversation. Extract findings in these categories:

| Category | Examples |
|---|---|
| **Root cause confirmed** | "The bug was X because Y — verified with data/tests" |
| **Pattern confirmed** | "We consistently do X for Y in this project" |
| **Anti-pattern found** | "Never do X — it causes Y (confirmed with evidence)" |
| **Decision made** | "We chose X over Y because Z" |
| **New constraint** | "Field X must always be Y", "This API requires Z format" |
| **Investigation insight** | "When symptom X appears, always check Y first" |
| **DB / data gotcha** | "Column X is VARCHAR not INT", "Use scope X not Y for this query type" |
| **Fix / solution** | "The fix was to use X instead of Y" |

**Skip:**
- Session-specific IDs (order 12345, user 99 — use `ORDER_ID`, `USER_ID` placeholders)
- Findings already documented (always read the target file before adding)
- Hypotheses that were NOT confirmed

---

## Step 2 — Map Each Finding to the Right Target

There are **two types** of knowledge to update. Choose the right one:

### Type A — Architectural Rules (reusable across any project)
These go into the skill's `rules/` and `workflows/` files.

| Finding type | Target |
|---|---|
| General coding rule | Closest rule file (`rules/code-quality.md`, `rules/models.md`, etc.) |
| Anti-pattern with wrong/correct example | `rules/anti-patterns.md` |
| Component-specific pattern | That component's rule file (`rules/actions.md`, `rules/filters.md`, etc.) |
| Multi-step process | Existing workflow or new `workflows/[name].md` |
| Project-level default | `SKILL.md` → Project Defaults section |

### Type B — Project-Specific Knowledge (features, data, environment)
These go into per-feature knowledge docs in `knowledge/[FEATURE].md`. Use this for:
- Bug root causes tied to specific feature behaviour
- Diagnostic queries worth reusing (with placeholder variables)
- Data gotchas (column types, table names, quirks)
- Investigated issues and their confirmed fixes
- Environment-specific facts (server paths, config values)

Knowledge location depends on the AI tool: `.claude/knowledge/` (Claude Code), `.agent/knowledge/` (Antigravity), or `knowledge/` at project root (Cursor/Copilot). One file per feature area — never one file per investigation ticket.

**Knowledge file template:**
```markdown
# [Feature Name] Knowledge

Last Updated: YYYY-MM-DD

## How It Works
Brief description of the feature's core flow.

## Issues Found & Fixed

### Issue 1: [Short title]
**Symptom**: What the user reported.
**Root cause**: What actually caused it (with data evidence).
**Fix**: What was changed.
**Diagnostic query** (if applicable):
\`\`\`sql
SELECT ... FROM ... WHERE feature_id = FEATURE_ID;
\`\`\`

## Key Lessons
- When [symptom X] → always check [Y] first
- [Column/field] is [type] not [assumed type]

## DB Gotchas
- `table.column` is VARCHAR not INT — use `'value'` not `12`
```

---

## Step 3 — Write in Directive Format

Rules for writing:
- MUST write actionable directives — "When X → do Y", not "We found that X"
- MUST use MUST / NEVER for absolute rules
- MUST use generic placeholders — `ORDER_ID`, `Car`, `User` — never session-specific names
- MUST include code/SQL examples when worth reusing
- NEVER write narrative prose ("In this session we investigated...")
- NEVER duplicate — read the target file before adding

**Good (directive format):**
```
**NEVER access a relationship in a Resource without `whenLoaded()`**
// Wrong — causes N+1 on every request
'brand' => $this->brand->name,

// Correct
'brand' => $this->whenLoaded('brand')?->name,
```

**Bad (narrative format):**
```
We found that in the CarResource, accessing the brand relationship directly
was causing N+1 queries because we didn't use whenLoaded(). This happened
during the cars refactor in March 2026 when we added the brand relationship.
```

---

## Step 4 — Update the Files

### For rule files:
1. Read the target file first
2. Add in the correct section — don't append randomly
3. Follow the file's existing format (MUST/NEVER + code block)

### For knowledge docs:
1. Read the existing file first (if it exists)
2. If it's a known issue type → add under "Issues Found"
3. If it's a reusable query → add under "Diagnostic Queries"
4. If it's a lesson → add under "Key Lessons"
5. If it's a data quirk → add under "DB Gotchas"
6. Update "Last Updated" date

### For new rule files, use this template:
```markdown
# [Rule / Component Name]

## Rules

- MUST ...
- NEVER ...

## [Pattern Name]

\`\`\`php
// Wrong
...

// Correct
...
\`\`\`
```

---

## Step 5 — Report

Tell the user exactly what changed:

```
Updated rules/models.md:
  → Added: ULID models must declare $keyType = 'string' and $incrementing = false

Updated rules/anti-patterns.md:
  → Added: NEVER access relationship in Resource without whenLoaded() (N+1)

Created knowledge/orders.md:
  → Documented root cause: order status is VARCHAR not INT — always quote it
  → Added diagnostic query: check stuck orders by status + created_at range

Nothing new for rules/controllers.md — already covered.
```

If nothing new: "All learnings from this session are already covered in the knowledge base."

---

## Anti-Patterns

❌ Writing narrative ("In this session we discovered...")
❌ Including specific IDs — use placeholders (`ORDER_ID`, `USER_ID`)
❌ Adding unconfirmed findings — only what was verified
❌ Creating a new rule file for a single one-off finding — add to an existing file
❌ Creating a new knowledge doc per investigation ticket — one file per feature
❌ Skipping the read-before-write check (duplicates)

✅ Write directives: "When X → do Y"
✅ Include reusable code/SQL with generic placeholders
✅ Separate architectural rules (rules/) from project knowledge (knowledge/)
✅ Keep findings brief and scannable
✅ Always read the target file before writing to it
