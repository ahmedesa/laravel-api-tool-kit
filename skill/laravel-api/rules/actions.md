# Action Classes

## When to Use

Use an Action when the operation has **multiple distinct steps**, even if all on the same model (e.g. create record + upload file + dispatch event). Also use when:
- Calls an external service (file upload, email, payment)
- Spans multiple models
- Needs to be reused across multiple controllers
- **Complex Syncing**: Operations like `sync()` for many-to-many relationships that involve extra logic (notifications, logging, auditing).
- **Naming**: Classes MUST be singular (`CreateUserAction`), and stored in plural parent folders (`Actions/`).

For simple `Model::create($data)` or `Model::update($data)` — do it directly in the controller. No Action needed.

## Actions vs Services

- **Actions:** Domain workflows — do ONE specific thing (`PlaceOrderAction`, `RegisterUserAction`, `ToggleLikeAction`). Accept data, orchestrate steps, return a model or void.
- **Services:** External 3rd-party integrations only (`SmsProvider`, `PaymentGateway`, `ExcelExporter`). See `rules/services.md`.

## Rules

- MUST be `final` — Actions are never extended; create a new Action instead
- MUST have a single public method named `execute()`
- MUST use `private readonly` constructor promotion for all dependencies
- MUST be injected into the controller via constructor — NEVER instantiated with `new`
- MUST return the resulting model, a collection, or `void` — NEVER return an HTTP response
- MUST use a DTO when any parameter would be `array` OR total params exceed 3 — see `rules/dtos.md`
- NEVER use the `new` keyword to instantiate an Action inside another class
- NEVER put HTTP concerns inside an Action (`request()`, `response()`, `redirect()`)
- NEVER put query building or filter logic in an Action

## Structure

```php
final class SendInvitationAction
{
    public function __construct(
        private readonly MailService $mail,
    ) {}

    public function execute(Team $team, string $email): Invitation
    {
        $invitation = $team->invitations()->create(['email' => $email]);

        InvitationSent::dispatch($invitation);

        $this->mail->sendInvite($invitation);

        return $invitation;
    }
}
```
