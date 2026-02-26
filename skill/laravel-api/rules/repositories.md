# Repositories

## When to Use a Repository

Use a Repository class to encapsulate **complex database queries**, especially for complex read operations (e.g. `getPopularPosts`, `searchInventory`). Repositories act as a centralized place to retrieve models, keeping controllers and actions thin and avoiding duplicated query logic.

For simple queries (e.g., `Post::where('status', 'active')->get()`), you can just use the Model directly in the Controller or Action.

## Rules

- MUST focus strictly on querying data (READ operations).
- NEVER put write operations (Create, Update, Delete) with side effects in Repositories — use Actions for that.
- MUST use `private readonly` constructor promotion for dependencies.
- NEVER use the `new` keyword to instantiate a Repository — always use constructor injection.
- MUST return Collections, a single Eloquent Model, or a `LengthAwarePaginator`.
- NEVER return raw JSON or HTTP responses.
- MUST use `dynamicPaginate()` for standard list/search queries to allow front-end control over page size.
- NEVER put HTTP concerns (`request()`) inside a Repository — pass parameters from the controller instead.

## Structure Pattern

```php
class PostRepository
{
    public function search(array $filters): LengthAwarePaginator
    {
        return Post::query()
            ->useFilters($filters)
            ->latest()
            ->dynamicPaginate();
    }

    public function getActivePostsForUser(User $user): Collection
    {
        return Post::query()
            ->where('user_id', $user->id)
            ->where('status', PostStatusEnum::ACTIVE)
            ->get();
    }
}
```
