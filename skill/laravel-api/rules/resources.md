# API Resources

All endpoints returning models MUST use API Resources — ensures consistent JSON, controls exposed fields, prevents N+1.

## Rules

- MUST use a Resource for every endpoint returning a model — NEVER return a raw model
- MUST use `$this->whenLoaded('relation')` for every relationship — NEVER access relations directly
- MUST use `$this->when($condition, $value)` for conditional fields — NEVER inline ternaries that null a field
- MUST use `dateTimeFormat()` for all timestamp fields — NEVER `->format()` or raw Carbon
- NEVER call `->exists()`, `->count()`, or `->first()` inside `toArray()` without an eager-load guard
- NEVER write business logic inside a Resource

## Structure

```php
class CarResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'is_active'  => $this->is_active,

            // Conditional field — key is omitted entirely when false
            'email'      => $this->when($request->user()?->is_admin, $this->email),

            // Relationships — only included when eager-loaded
            'brand'      => new BrandResource($this->whenLoaded('brand')),
            'tags'       => TagResource::collection($this->whenLoaded('tags')),

            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
```

## Boolean from Relationship

```php
'is_liked' => $this->when(
    $this->relationLoaded('currentUserLike'),
    fn() => $this->currentUserLike->isNotEmpty()
),
```
