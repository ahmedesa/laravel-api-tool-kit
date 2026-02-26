# Workflow: Deep Project Scan

Run this at the **start of a new session** or when working in an unfamiliar project. It performs a comprehensive analysis of both structural conventions and custom project abstractions, then writes all findings into the SKILL.md **Project Defaults** section.

**Trigger phrases**: "scan project", "deep scan", "learn project", "collect patterns", "what conventions does this project use", "scan before we start"

> After running this, you will know the project's **true vocabulary**. Every generated file will use the same classes, services, and core architectural patterns already in use.

---

## Phase 1 — Structural Discovery

### Step 1 — Project Layout & Core Structure
Scan the root and `app/` directory to determine the architectural layout:
- **Standard**: `app/Models`, `app/Actions`, `app/Http/Controllers`
- **DDD**: `app/Domain/{Domain}/Models`, `app/Domain/{Domain}/Actions`

If DDD, list `app/Domain/` to identify domain names and check for folder naming variations (e.g., `Repository` vs `Repositories`).

### Step 2 — Database & Identity
Inspect `database/migrations` and `app/Models/User.php`:
- **Primary Keys**: `ulid`, `uuid`, or incrementing `id`.
- **Identity**: Check if `HasUuids` or `HasUlids` traits are used in models.

### Step 3 — Environment & Configuration
- **Auth Guard**: Check `config/auth.php` and `routes/api.php` for `sanctum`, `api`, or custom guards.
- **Queue**: Check `config/queue.php` for the default connection (redis, database, sync).
- **Cache**: Check `config/cache.php` and look for a `CacheKeys` enum or constant class.

---

## Phase 2 — Deep Infrastructure Scan

### Step 4 — Service Layer Analysis
Perform a deep scan of `app/Services`, `app/Core`, and `app/Infrastructure`:
- **Discovery**: List all subdirectories in `app/Services`.
- **Pattern**: Look for `Interface` vs `Concrete` bindings.
- **Key Services**: Identify classes for **SMS**, **Payments**, **File Storage**, **External APIs**, and **3rd Party Integrations**.

**Record**: "For SMS, the project uses `SmsService`. For storage, it uses `MediaLibrary`."

### Step 5 — Communication & Messaging
Identify how the project handles internal and external communication:
- **Notifications**: Check `app/Notifications` for custom channels (SMS, Push, Slack).
- **Events**: Check for a `BaseEvent` or shared event interfaces.
- **Mail**: Check `app/Mail` for custom mailable patterns.

### Step 6 — Feature Flags & A/B Testing
Search for toggling patterns:
- **Package**: `laravel/pennant` in `composer.json`?
- **Custom**: Grep for `Feature::`, `Flag::`, or check `config/features.php`.

### Step 7 — Core Base Classes & Middleware
Identify the "DNA" of the project:
- **Base Components**: Check for `BaseController`, `BaseAction`, `BaseRepository`, or `BaseModel`.
- **Middleware**: Scan `app/Http/Middleware` for tenant detection, localization, or custom security logic.
- **Traits**: Search for global traits like `HasAudit`, `HasSlug`, `HasTranslations`, or `MultiTenant`.

---

## Phase 3 — Pattern Discovery (Grep Scan)

### Step 8 — Enum Pattern

Check how enums are implemented:

| Pattern | Convention |
|-------|-----------|
| PHP backed enums (standard) | Standard PHP |

### Step 9 — Abstract Pattern Detection
Execute a broad search across the `app/` directory to find architectural anchors:
- Search for classes containing `Abstract`, `Interface`, `Contract`, `Base`, or `Service`.
- Identify the project's specific naming for DTOs (e.g., `Data`, `Dto`, `TransferObject`).

### Step 10 — Error & Exception Handling
- **Exceptions**: Scan `app/Exceptions` for custom domain exceptions.
- **Responses**: Check the base Controller for custom response wrappers (e.g., `responseError`, `responsePaginated`).

---

## Phase 4 — Write & Report

### Step 10 — Update SKILL.md Project Defaults
Open the installed SKILL.md and populate the **Project Defaults** section with the deep scan findings:

```markdown
## Project Defaults

### Structure & Core
- Layout: [Standard | DDD]
- Primary Keys: [ulid | uuid | id]
- Auth Guard: [sanctum | api | other]
- Test Base: [Tests\TestCase | Other]

### Deep Infrastructure
- Service Logic: [Standard Services | Interface/Concrete Pattern]
- SMS/Messaging: [Class name or Service]
- Feature Flags: [Package or Pattern]
- Feature Toggles: [Pennant / custom / config]
- Core Traits: [HasTenant, HasAudit, etc.]
- Cache Strategy: [CacheKeys location / wrapper]
- Base Classes: [BaseAction, BaseRepository, etc.]
- Queue Driver: [redis / database]
- Multi-tenancy: [Scope name or Middleware]
```

### Step 11 — Comprehensive Report
Provide a detailed summary of discovered patterns, including specific file paths or classes that the AI should prioritize using. Example:

```text
Deep Project Scan Complete:
- Core: DDD layout with ULID primary keys.
- Services: Interface-bound services in app/Domain/{Domain}/Services.
- Messaging: Custom SmsGateway (Twilio).
- Flags: Laravel Pennant in use for feature toggles.
- Core: All Actions must extend app/Core/AbstractAction.
- Traits: Models use ipera/multi-tenancy for scoping.
- Updated SKILL.md Project Defaults ✓
```
