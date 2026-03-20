# API Responses

## Rules

- MUST use `ApiResponse` trait methods only — NEVER `response()->json()` directly
- MUST use `trans()` for every `$message` argument — NEVER hardcode user-facing strings
- MUST use `responseCreated()` for store, `responseDeleted()` for destroy
- MUST wrap model data in a Resource before passing as `$data` — NEVER pass raw models
- NEVER use `abort()` for business logic — throw `UnprocessableEntityHttpException` or use `responseUnprocessable()`

## Method Reference

**Success methods** — parameter order is `($message, $data)`:

| Method | Status | Use for |
|--------|--------|---------|
| `responseSuccess(?string $message, mixed $data)` | 200 | show, update, custom actions |
| `responseCreated(?string $message, mixed $data)` | 201 | store |
| `responseAccepted(?string $message, mixed $data)` | 202 | queued / async operations |
| `responseDeleted()` | 204 | destroy — no body |
| `responseNoContent()` | 204 | any operation returning no body |

**Error methods** — parameter order is `($details, $message)` (reversed from success methods):

| Method | Status | Use for |
|--------|--------|---------|
| `responseUnprocessable($details, $message)` | 422 | business rule violation |
| `responseNotFound($details, $message)` | 404 | record not found |
| `responseBadRequest($details, $message)` | 400 | malformed request |
| `responseUnAuthenticated($details, $message)` | 401 | not logged in |
| `responseUnAuthorized($details, $message)` | 403 | logged in but forbidden |
| `responseConflictError($details, $message)` | 409 | duplicate / state conflict |
| `responseServerError($details, $message)` | 500 | unexpected server error |
| `responseWithCustomError($title, $details, $statusCode)` | custom | any other error |

**Note on error methods:** The first argument is `$details`, not `$message`. When calling `responseUnprocessable(trans('car.limit_reached'))`, the translated string goes into the `detail` field of the error response. This is the correct and intended usage.

## Examples

```php
return $this->responseCreated(trans('car.created'), new CarResource($car));
return $this->responseSuccess(null, new CarResource($car));
return $this->responseSuccess(trans('car.updated'), new CarResource($car));
return $this->responseDeleted();
return $this->responseUnprocessable(trans('car.limit_reached'));

// Collection / index endpoints — use Resource::collection() directly.
// This is the correct pattern for lists; it is NOT a violation of the ApiResponse rule.
// Return type is AnonymousResourceCollection, not JsonResponse.
return CarResource::collection(Car::useFilters()->dynamicPaginate());
```
