# API Responses

## Rules

- MUST use `ApiResponse` trait methods only — NEVER `response()->json()` directly
- MUST use `trans()` for every `$message` argument — NEVER hardcode user-facing strings
- MUST use `responseCreated()` for store, `responseDeleted()` for destroy
- MUST wrap model data in a Resource before passing as `$data` — NEVER pass raw models
- NEVER use `abort()` for business logic — throw `UnprocessableEntityHttpException` or use `responseUnprocessable()`

## Method Reference

| Method | Status | Use for |
|--------|--------|---------|
| `responseSuccess($msg, $data)` | 200 | show, update, custom actions |
| `responseCreated($msg, $data)` | 201 | store |
| `responseAccepted($msg, $data)` | 202 | queued / async operations |
| `responseDeleted()` | 204 | destroy — no body |
| `responseNotFound()` | 404 | record not found |
| `responseBadRequest()` | 400 | malformed request |
| `responseUnAuthenticated()` | 401 | not logged in |
| `responseUnAuthorized()` | 403 | logged in but forbidden |
| `responseConflictError()` | 409 | duplicate / state conflict |
| `responseUnprocessable($msg)` | 422 | business rule violation |

## Examples

```php
return $this->responseCreated(trans('car.created'), new CarResource($car));
return $this->responseSuccess(null, new CarResource($car));
return $this->responseSuccess(trans('car.updated'), new CarResource($car));
return $this->responseDeleted();
return $this->responseUnprocessable(trans('car.limit_reached'));
return CarResource::collection(Car::useFilters()->dynamicPaginate());
```
