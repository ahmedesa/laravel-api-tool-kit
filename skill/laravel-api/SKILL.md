---
name: laravel-api-tool-kit
description: >
  Build production-grade Laravel REST APIs. Covers general best practices
  (code quality, DI, events, auth, exceptions, testing, database) AND
  essa/api-tool-kit specific patterns (ApiResponse, QueryFilters, dynamicPaginate, EnumHelpers).
  Use when creating or reviewing any Laravel API code in a project that has essa/api-tool-kit installed.
  Triggers on: "create a controller", "add an endpoint", "add filtering", "create a model",
  "create a resource", "create an action", "create an event", "review this API code",
  "add pagination", "write a test", "how should I structure this feature".
---

# Laravel API Tool Kit Skill

This project uses the `essa/api-tool-kit` package. All API code MUST follow the standards in this skill — both the general Laravel best practices and the package-specific patterns.

## Assumed Setup

- `essa/api-tool-kit` is installed via Composer
- Base `Controller` already uses the `ApiResponse` trait
- The `dateTimeFormat()` global helper is available
- `dynamicPaginate()` macro is registered on the query builder

## Project Defaults

**Fill these in when installing the skill.** The AI reads this section to match existing project conventions.

- **Primary key type**: `ulid` ← change to `id` if this project uses auto-increment
- **Auth guard**: `sanctum` ← change if different (e.g. `api`)
- **Test class**: `Tests\TestCase` ← change if the project uses a custom base

## Structure Mapping & Customization

The rules in the `rules/` directory are **pattern-based**. While examples use standard Laravel paths (e.g., `app/Models`), they are designed to be mapped to any structure.

### DDD / Domain-Driven Design
If the project uses a DDD structure, the rules apply to the corresponding domain folders. **Folder naming may vary by project** (e.g., `Repository` vs `Repositories`). Examples include:
- **Models**: `app/Domain/{Domain}/Models/`
- **Actions**: `app/Domain/{Domain}/Actions/`
- **Repositories**: `app/Domain/{Domain}/Repository/` (or `Repositories/`)
- **DTOs**: `app/Domain/{Domain}/DTO/` (or `DTOs/`)
- **Filters**: `app/Domain/{Domain}/Filters/`

**Note for AI**: Always perform a `list_dir` on `app/Domain/` to identify the project's specific naming conventions before creating new files. Priority is always: **Project Patterns > Global Rules**.

### Project-Specific Rules
If a specific project requires overrides (e.g., "We use `id` instead of `ulid`" or "We return raw arrays instead of DTOs"), **do not modify these global rules**. Instead:
1. Identify the project's knowledge base (usually `.agents/instructions.md` or `ai-rules/AI_RULES.md`).
2. Add the override there. The AI will prioritize project-level instructions over these global patterns.

## Baseline — Applies to Every File

Before reading anything else, these rules apply universally:

- Every PHP file MUST start with `declare(strict_types=1);`
- Every method MUST have parameter types and a return type
- Constructor dependencies MUST use `private readonly` promotion
- User-facing strings MUST use `trans()` — never hardcoded

See `rules/code-quality.md` for the full baseline.

## Component Map

When working on a specific concern, read the corresponding rule file:

### General Standards (applies everywhere)
| Task | Read |
|------|------|
| Code style, types, naming, constants, DI | `rules/code-quality.md` |
| Events and listeners | `rules/events.md` |
| Authorization and policies | `rules/authorization.md` |
| Error and exception handling | `rules/exceptions.md` |
| Writing feature tests | `rules/testing.md` |
| Database patterns, ULIDs, transactions, bulk ops | `rules/database.md` |
| External 3rd-party integrations | `rules/services.md` |
| Media and file management | `rules/media.md` |

### Package-Specific Patterns
| Task | Read |
|------|------|
| Return a JSON response | `rules/responses.md` |
| Add filtering / sorting / search to an endpoint | `rules/filters.md` |
| Create a new Action class | `rules/actions.md` |
| Create a DTO for an Action or Service | `rules/dtos.md` |
| Write or review a Controller | `rules/controllers.md` |
| Write a FormRequest | `rules/requests.md` |
| Write an API Resource | `rules/resources.md` |
| Create or update a Model | `rules/models.md` |
| Create or update a Repository | `rules/repositories.md` |
| Create an Enum | `rules/enums.md` |
| Add pagination | `rules/pagination.md` |

### Always Check First
| Task | Read |
|------|------|
| Before writing any code | `rules/anti-patterns.md` |

## Building a New Endpoint

Follow `workflows/new-endpoint.md` for the full step-by-step process.

The order is always:
**Model → Migration → Filter → Requests → Resource → Action (if needed) → Controller → Route → Test**

## Available Workflows

- `workflows/scan-project.md` — scan a project to discover conventions and populate Project Defaults
- `workflows/new-endpoint.md` — add a complete CRUD resource from scratch
- `workflows/add-filter.md` — add filtering to an existing model
- `workflows/code-review.md` — checklist for reviewing any API code
- `workflows/update-knowledge.md` — save learnings from the current session into the skill
