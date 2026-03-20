# Dependency Injection

## Rules

- MUST use constructor injection with `private readonly` as the default — always
- MUST type-hint all constructor parameters
- NEVER use `new` to instantiate services, actions, or repositories inside methods
- NEVER use `resolve()` or `app()` inside a constructor
- NEVER make static calls on injectable classes
- `app()` is acceptable only inside traits or static methods where constructor injection is impossible
- `new` is acceptable only for value objects and simple data classes (no container dependencies)

## Correct

```php
class CarController extends Controller
{
    public function __construct(
        private readonly CreateCarAction $createCar,
        private readonly CarRepository $cars,
    ) {}
}
```

## Wrong

```php
public function store(CreateCarRequest $request): JsonResponse
{
    $action = new CreateCarAction();   // NEVER
    $repo   = resolve(CarRepository::class); // NEVER in constructor
}
```
