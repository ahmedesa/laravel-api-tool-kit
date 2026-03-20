# Domain-Driven Design (DDD)

This file applies when the project uses a DDD/domain-based folder structure. Skip if using standard Laravel structure.

## Core Philosophy

- **High Cohesion**: Everything that defines a feature (Model, Actions, Filters, Events) lives together in its Domain folder
- **Loose Coupling**: Domains MUST NOT tightly depend on the internal workings of other Domains
- **Single Source of Truth**: A Model is only managed by its own Domain

## Directory Structure

```
app/
├── Domain/
│   ├── Order/
│   │   ├── Models/
│   │   ├── Actions/
│   │   ├── DTOs/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   ├── Enums/
│   │   ├── Exceptions/
│   │   ├── Filters/
│   │   ├── Repository/
│   │   ├── Policies/
│   │   ├── Traits/
│   │   └── Providers/
│   ├── User/
│   └── ...
├── Http/
│   ├── Controllers/API/
│   │   ├── Application/     ← End-user facing
│   │   │   └── Order/
│   │   └── Dashboard/       ← Admin facing
│   │       └── Order/
│   ├── Requests/
│   │   ├── Application/
│   │   └── Dashboard/
│   └── Resources/
│       ├── Application/
│       └── Dashboard/
├── Services/                 ← External integrations only (Stripe, S3, SMS)
├── Support/                  ← Base classes, shared utilities
│   ├── Models/BaseModel.php
│   └── Repositories/BaseRepository.php
└── Infrastructure/           ← Cross-cutting (auth cache, custom channels)
```

## What Goes Where

| Thing | Location | Why |
|---|---|---|
| Model, Action, DTO, Event, Enum, Filter, Repository | `app/Domain/{Name}/` | Domain logic stays in the domain |
| Controller, Request, Resource | `app/Http/{audience}/{Domain}/` | HTTP is a transport concern |
| External service (Stripe, SMS, S3) | `app/Services/{Service}/` | Domain-agnostic, multiple domains use it |
| Base classes, shared helpers | `app/Support/` | No domain owns these |
| Auth cache, custom channels | `app/Infrastructure/` | Cross-cutting infrastructure |

## What is NOT in the Domain

Domains contain the **What** and **How** of business logic. The application layer contains the **Who** and **When**.

| Stays outside Domain | Why |
|---|---|
| Controllers (`app/Http/Controllers/`) | HTTP transport mechanism, not business logic |
| Requests (`app/Http/Requests/`) | Validation is a transport concern |
| Resources (`app/Http/Resources/`) | JSON formatting is an output concern |
| Console Commands (`app/Console/`) | CLI orchestration is an entry point |
| Broadcasting Channels (`routes/channels.php`) | Broadcasting is a transport mechanism |

---

## Cross-Domain Boundaries

Domains MUST respect each other's boundaries.

### 1. Database Encapsulation (Strict)

Repositories and Models MUST NOT use SQL joins or eager loading (`with()`) for tables belonging to other domains.

```php
// WRONG — PostRepository joins the users table (User domain)
Post::query()
    ->join('users', 'users.id', '=', 'posts.user_id')
    ->select('posts.*', 'users.name')
    ->get();

// WRONG — eager loading into another domain
Post::with('user.profile')->get();
```

### 2. App-Level Merging (Cross-Domain Data)

When an endpoint needs data from multiple domains (e.g., a Post and its Author), use **Application-Level Hydration**:

```php
// Step 1: Domain A returns its own data (with foreign IDs)
$posts = $this->postRepository->getLatest(); // returns posts with user_id

// Step 2: Collect IDs, fetch from Domain B
$userIds = $posts->pluck('user_id')->unique();
$users   = User::whereIn('id', $userIds)->get()->keyBy('id');

// Step 3: Merge in PHP (in Controller or Application-layer Action)
// The Resource handles the assembly
return PostResource::collection($posts)->additional(['users' => $users]);
```

This keeps each domain's queries independent — no cross-domain joins.

### 3. Shadow Models (Advanced)

To fully isolate domains, a domain can define its own local model for an external table:

```php
// app/Domain/Social/Models/SocialMember.php
// Maps to the `users` table but only has social-domain relationships
class SocialMember extends Model
{
    protected $table = 'users';

    public function friendships(): HasMany { ... }
    public function blocks(): HasMany { ... }
}
```

This prevents the `User` model from accumulating traits from every domain (`Friendable`, `HasPosts`, `HasSubscription`, etc.).

### 4. Communication Between Domains

```php
// Asynchronous side effects → Events
PostCreated::dispatch($post);
// User domain's listener increments posts_count

// Synchronous operations → Inject the other domain's Action
public function __construct(
    private readonly ProcessReferralReward $processReferralReward,
) {}
```

### Communication Pattern Summary

| Requirement | Pattern |
|---|---|
| Endpoint needs data from multiple domains | App-Level Merging (fetch IDs → hydrate in PHP) |
| Side-effect in another domain | Domain Events → Listeners |
| Direct operation in another domain | Action Injection |
| Cross-domain relationship check | Action Injection or Repository call |

---

## Domain ServiceProviders

Each domain registers its own events and bindings:

```php
// app/Domain/Order/Providers/OrderServiceProvider.php
class OrderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OrderPlaced::class, SendOrderConfirmation::class);
        Event::listen(OrderPlaced::class, UpdateInventory::class);
    }
}
```

Register in `bootstrap/providers.php`. Do NOT dump all domain events into a single global `EventServiceProvider`.

---

## Audience Splitting

If the same resource is exposed to both end-users and admins with different fields:
- `app/Http/Resources/Application/Order/OrderResource.php` — shows status, items, total
- `app/Http/Resources/Dashboard/Order/OrderResource.php` — also shows internal notes, cost, margin

NEVER use conditionals inside one Resource to handle both audiences. Create separate Resources.
