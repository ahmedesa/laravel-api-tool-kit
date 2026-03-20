# Workflow: Organize Knowledge Base

Consolidates scattered investigation docs, notes, and findings into the correct knowledge files. Prevents knowledge sprawl and ensures ONE file per feature.

**Trigger phrases**: "organize knowledge", "consolidate docs", "clean up knowledge", `/organize-knowledge`

---

## The Problem This Solves

Over time, investigations produce scattered files:
- `INVESTIGATION_ORDER_BUG.md`
- `FIX_WALLET_ISSUE.md`
- `NOTES_ON_CACHE_PROBLEM.md`
- Duplicate findings across multiple files

This workflow consolidates everything into the correct `knowledge/[FEATURE].md` file — one file per feature, not one file per ticket.

---

## Step 1 — Find Scattered Docs

Search for investigation artifacts that should be consolidated:

```
*_INVESTIGATION.md
INVESTIGATION_*.md
*_FIX.md
*_NOTES.md
*_DEBUG.md
*_FINDINGS.md
```

Also check:
- `knowledge/` for files that overlap in topic
- Root directory for stray markdown files
- `.claude/knowledge/`, `.agent/knowledge/`, or `knowledge/` for orphaned docs

---

## Step 2 — Map Each Doc to a Feature

For each scattered doc, determine which feature it belongs to:

| Doc | Feature Area | Target File |
|---|---|---|
| `INVESTIGATION_ORDER_BUG.md` | Orders | `knowledge/orders.md` |
| `FIX_WALLET_ISSUE.md` | Wallet | `knowledge/wallet.md` |
| `CACHE_NOTES.md` | General | `rules/anti-patterns.md` or `knowledge/caching.md` |

**Rules:**
- If a feature knowledge file already exists → merge into it
- If no file exists → create from `knowledge/_TEMPLATE.md`
- If the finding is a general rule (not feature-specific) → add to the appropriate `rules/*.md` file
- NEVER create two files for the same feature

---

## Step 3 — Extract and Classify Findings

From each scattered doc, extract:

| Finding Type | Goes Into |
|---|---|
| Root cause of a bug | Knowledge file → "Issues Found & Fixed" |
| Diagnostic query that was useful | Knowledge file → "Diagnostic Queries" |
| Lesson / pattern discovered | Knowledge file → "Key Lessons" |
| DB column type gotcha | Knowledge file → "DB Gotchas" |
| General coding rule | `rules/code-quality.md` or closest rule file |
| Anti-pattern with wrong/correct | `rules/anti-patterns.md` |

**Convert narrative to directive:**
- ❌ "We found that the wallet balance was wrong because currency_id was missing"
- ✅ "When wallet balance is wrong → check if currency_id is included in the query. Missing currency_id causes the conversion trait to skip currency conversion silently."

---

## Step 4 — Merge Into Target Files

For each target knowledge file:
1. Read the existing file
2. Check for duplicates (don't add what's already there)
3. Add findings to the correct section
4. Update "Last Updated" date

For new knowledge files:
1. Copy `knowledge/_TEMPLATE.md` → `knowledge/[feature].md`
2. Fill in "How It Works" section
3. Add all findings from scattered docs

---

## Step 5 — Archive or Delete Scattered Docs

After merging:
- Delete the scattered investigation files (the knowledge is now in the right place)
- If unsure, move to a `knowledge/_archived/` directory first

---

## Step 6 — Report

```
Consolidated 4 scattered docs into knowledge files:

knowledge/orders.md (created):
  → Merged INVESTIGATION_ORDER_BUG.md (root cause + 2 queries)
  → Merged ORDER_NOTES.md (3 lessons)

knowledge/wallet.md (updated):
  → Merged FIX_WALLET_ISSUE.md (1 issue + 1 gotcha)

rules/anti-patterns.md (updated):
  → Added: NEVER skip currency_id in wallet queries

Deleted: INVESTIGATION_ORDER_BUG.md, ORDER_NOTES.md, FIX_WALLET_ISSUE.md
```

---

## Anti-Patterns

❌ Multiple knowledge files for the same feature (orders_investigation.md AND orders_bugs.md)
❌ Keeping session-specific IDs in consolidated docs
❌ Narrative prose ("On Tuesday we found...") — use directives
❌ Creating a knowledge file when a `rules/*.md` file is the right home
❌ Leaving scattered docs after consolidating (creates confusion about which is authoritative)

✅ ONE file per feature area
✅ Directive format: "When X → check Y"
✅ Generic placeholders: `ORDER_ID`, `USER_ID`
✅ Delete originals after merging
