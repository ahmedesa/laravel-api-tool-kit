# Controllers

Controllers receive HTTP requests, delegate to the appropriate layer, and return responses. They contain NO business logic.

## Rules

- MUST extend the app's base `Controller`
- MUST use `$request->validated()` — NEVER `$request->all()` or `$request->except()`
- MUST use `ApiResponse` trait methods — NEVER `response()->json()` directly
- MUST wrap all model data in a Resource before returning
- MUST use `$this->authorize()` before any write operation — NEVER inline auth checks
- MUST use `trans()` for all user-facing message strings — NEVER hardcode strings
- MUST use `responseCreated()` for store, `responseDeleted()` for destroy
- MUST apply `throttle` middleware to sensitive endpoints (login, store, password reset, OTP) — NEVER leave write endpoints unthrottled
- NEVER write query logic in a controller — use filters or scopes
- NEVER write business logic in a controller — use Actions or model methods

## Standard CRUD

```php
class CarController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CarResource::collection(Car::useFilters()->dynamicPaginate());
    }

    public function store(CreateCarRequest $request): JsonResponse
    {
        $car = Car::create($request->validated());
        return $this->responseCreated(trans('car.created'), new CarResource($car));
    }

    public function show(Car $car): JsonResponse
    {
        return $this->responseSuccess(null, new CarResource($car));
    }

    public function update(UpdateCarRequest $request, Car $car): JsonResponse
    {
        $this->authorize('update', $car);
        $car->update($request->validated());
        return $this->responseSuccess(trans('car.updated'), new CarResource($car));
    }

    public function destroy(Car $car): JsonResponse
    {
        $this->authorize('delete', $car);
        $car->delete();
        return $this->responseDeleted();
    }
}
```

## With an Injected Action

```php
class CarController extends Controller
{
    public function __construct(
        private readonly CreateCarAction $createCar,
    ) {}

    public function store(CreateCarRequest $request): JsonResponse
    {
        $car = $this->createCar->execute($request->validated());
        return $this->responseCreated(trans('car.created'), new CarResource($car));
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth:sanctum');
    $this->middleware('throttle:10,1')->only(['store']);
}
```
