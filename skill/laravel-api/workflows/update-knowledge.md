# Workflow: Update Knowledge Base

Run this after completing a feature, fixing a bug, or making an architectural decision to preserve learnings for future sessions.

**Trigger phrases**: "update knowledge", "remember this", "save what we learned", "update the skill"

---

## Step 1 — Scan the Conversation

Review the full conversation history. Extract findings in these categories:

| Category | Examples |
|---|---|
| **New constraint** | "Field X must always be Y", "This API requires Z format" |
| **Solution** | "The fix was to use X instead of Y" |
| **Pattern confirmed** | "We consistently use X for Y in this project" |
| **Anti-pattern found** | "Never do X — it causes Y" |
| **Decision made** | "We chose X approach over Y" |

Skip anything already documented. Skip session-specific context (current task, in-progress work, temp state).

---

## Step 2 — Classify Each Finding

Map each finding to the correct target:

| Finding type | Target |
|---|---|
| General coding rule or constraint | `rules/code-quality.md` or the closest rule file |
| Component-specific pattern | That component's rule file (e.g., `rules/models.md`) |
| Anti-pattern | `rules/anti-patterns.md` — add a wrong/correct pair |
| Multi-step process | Add to an existing workflow or create a new one in `workflows/` |
| Project-level fact | `SKILL.md` → Project Defaults section |

---

## Step 3 — Update the Files

Make targeted edits only. Rules for this step:

- NEVER create a new rule file for a single finding — add to an existing file
- NEVER write narrative prose ("today we found...") — write directives only: MUST / NEVER + code example
- NEVER duplicate existing rules — check the file before adding
- NEVER include project-specific names from the current session (class names, route names, column names) — use generic examples (`Car`, `Order`, `User`)
- MUST keep the AI-directive format: short, imperative, no "why" explanations

---

## Step 4 — Report

Tell the user:

- Which files were updated and what was added (one line per change)
- If nothing new was found: "All learnings from this session are already covered in the skill"
