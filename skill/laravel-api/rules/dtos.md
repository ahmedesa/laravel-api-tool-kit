# DTOs (Data Transfer Objects)

## When to Use

Use a DTO when an Action or Service method would have:

- **Any `array` parameter** — raw arrays are ambiguous; replace with a typed DTO
- **4 or more parameters** total — group them into a DTO

```php
// No DTO — 2 typed params, unambiguous
public function execute(User $user, string $newPassword): void

// DTO required — array is ambiguous
public function execute(User $user, UpdateUserData $data): User

// DTO required — 4+ params
public function execute(Order $order, string $coupon, string $gateway, bool $notify): void
// becomes →
public function execute(Order $order, ProcessPaymentData $data): void
```

Simple `array $data` pass-through from `$request->validated()` for a basic `Model::create($data)` is acceptable — no DTO needed.

## Rules

- MUST be `final readonly class` — DTOs are never extended or mutated
- MUST use `private` constructor promotion (`readonly` is implicit on the class)
- MUST expose values via getter methods — NEVER public properties
- MUST use `Data` suffix: `UpdateUserData`, `ProcessPaymentData`, `SendNotificationData`
- MUST co-locate with the Action or Service that uses it
- MUST use the `new` keyword to instantiate a DTO — NEVER use Dependency Injection for DTOs
- NEVER add setters — DTOs are immutable
- NEVER pass `array` when the array contains multiple named fields — use a DTO

## Structure

```php
readonly class UpdateUserData
{
    public function __construct(
        private ?string $name,
        private ?string $email,
        private ?string $phone,
    ) {}

    public function getName(): ?string { return $this->name; }
    public function getEmail(): ?string { return $this->email; }
    public function getPhone(): ?string { return $this->phone; }
}
```

## Usage in Action

```php
class UpdateUserProfileAction
{
    public function execute(User $user, UpdateUserData $data): User
    {
        $user->update([
            'name'  => $data->getName(),
            'email' => $data->getEmail(),
            'phone' => $data->getPhone(),
        ]);

        return $user;
    }
}
```

## Constructing from Request (in Controller)

```php
$action->execute($user, new UpdateUserData(
    name:  $request->validated('name'),
    email: $request->validated('email'),
    phone: $request->validated('phone'),
));
```
