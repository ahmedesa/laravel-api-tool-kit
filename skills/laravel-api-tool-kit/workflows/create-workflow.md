# Workflow: Create a New Workflow

Use this when you discover a repeatable process during a session — an investigation type, a setup procedure, a debugging pattern — that should be captured as a reusable workflow.

**Trigger phrases**: "create workflow", "make a workflow for this", "save this as a workflow", "this should be a workflow", `/create-workflow`

**Also trigger proactively** — if you just finished an investigation or multi-step process and realize someone will hit this again, suggest creating a workflow.

---

## When to Create a Workflow

A workflow is worth creating when:
- You just completed a **multi-step process** that took real effort to figure out
- The steps would be **the same next time** (not a one-off)
- Someone (including future-you) would otherwise have to **rediscover** these steps
- The knowledge doesn't fit in a rule file (it's procedural, not declarative)

Examples from real projects:
- `server-investigator.md` — born from debugging a staging server with no root access
- `firebase-investigator.md` — born from setting up Firebase across 3 platforms
- `stripe-integration.md` — born from integrating Stripe subscriptions with DDD

---

## Step 1 — Extract the Process

Review the current conversation and identify:

1. **What was the goal?** (e.g., "Debug why emails aren't sending on staging")
2. **What steps did you take?** List them in order
3. **What was non-obvious?** Which steps required trial-and-error or knowledge you didn't have?
4. **What would you do differently next time?** Optimize the path
5. **What tools/commands were needed?** (artisan, SQL, curl, ssh, etc.)

---

## Step 2 — Structure the Workflow

Use this template:

```markdown
# Workflow: [Name] ([Short Context])

[One sentence: when to use this workflow.]

**Trigger phrases**: "[keyword1]", "[keyword2]", `/[command-name]`

---

## Prerequisites

- [What must be true before starting]
- [Required access, tools, or packages]

---

## Architecture / Context

[Brief description of HOW the system works, so the reader understands
WHY the steps are in this order. Include:]
- Key files / tables / services involved
- Common configurations
- Constraints (no root access, specific PHP version, etc.)

---

## Investigation / Execution Steps

### Step 1: [First thing to check/do]
[Why this step first]
\`\`\`bash
[Command or query]
\`\`\`
[What to look for in the output]

### Step 2: [Next step]
...

---

## Common Scenarios & Fixes

### Scenario A: [Problem Title]
**Symptom**: [What you see]
**Cause**: [Why it happens]
**Fix**:
\`\`\`bash
[Command or code to fix]
\`\`\`

### Scenario B: [Problem Title]
...

---

## Anti-Patterns / Usage Rules

- NEVER [common mistake that wastes time]
- ALWAYS [thing that saves time]
- If [condition] → [do this instead]
```

---

## Step 3 — Include Battle-Tested Commands

If the workflow involves commands, include the EXACT commands that worked — not generic examples. Include:
- Artisan commands with real flags
- SQL queries with placeholder variables (`STORE_ID`, `USER_ID`)
- Curl commands with correct headers
- SSH/server commands with correct paths
- Tinker one-liners

These are the most valuable part. A workflow without commands is just documentation.

---

## Step 4 — Add Common Scenarios

The best workflows have a "Scenarios & Fixes" section that covers:
- The 3-5 most common variations of this problem
- Symptoms → Cause → Fix for each
- Specific error messages and what they mean

This turns an investigation workflow into a **lookup table** — next time you see the symptom, you skip straight to the fix.

---

## Step 5 — Place the File

Save to `workflows/[descriptive-name].md`:
- Investigation workflows: `workflows/[feature]-investigator.md`
- Setup procedures: `workflows/[feature]-setup.md`
- Integration guides: `workflows/[service]-integration.md`
- Operational runbooks: `workflows/[operation]-runbook.md`

Then update the main skill entry point (`SKILL.md` or `instructions.md`) → "Available Workflows" section.

---

## Step 6 — Report

Tell the user:
```
Created workflows/queue-debugger.md:
  → 5 investigation steps (check failed_jobs → check Horizon → check listener → ...)
  → 3 common scenarios (Redis down, listener exception, job serialization)
  → 8 diagnostic commands

Updated SKILL.md → Added to Available Workflows list
```

---

## Quality Checklist

Before saving, verify:
- [ ] Trigger phrases are natural (what would someone actually type?)
- [ ] Architecture section explains enough context to follow the steps
- [ ] Steps are in the order you'd actually do them (optimized, not chronological)
- [ ] Commands are copy-pasteable (real flags, correct paths)
- [ ] Common scenarios have symptom → cause → fix (not just "check X")
- [ ] Anti-patterns section prevents the time-wasting mistakes you made
- [ ] Placeholder variables used instead of real IDs (`ORDER_ID` not `12345`)
