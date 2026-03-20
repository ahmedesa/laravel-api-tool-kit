# Form Requests

## Rules

- MUST create separate requests for create and update: `Create{Model}Request`, `Update{Model}Request`
- MUST always call `$request->validated()` in controllers — NEVER `$request->all()` or `$request->except()`
- `authorize()` MUST return `true` — put authorization in Policies, not here
- Update request rules MUST use `sometimes` to allow partial updates
- NEVER validate inside a controller method

## Create Request

```php
class CreateCarRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'color'    => ['required', 'string'],
            'model_id' => ['required', 'exists:car_models,id'],
            'status'   => ['required', Rule::in(CarStatusEnum::values())],
        ];
    }
}
```

## Update Request

```php
class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'color'    => ['sometimes', 'string'],
            'model_id' => ['sometimes', 'exists:car_models,id'],
            'status'   => ['sometimes', Rule::in(CarStatusEnum::values())],
        ];
    }
}
```
