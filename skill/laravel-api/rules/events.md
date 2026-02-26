# Events & Listeners

Use events when an action has side effects that should be decoupled or queued (notifications, analytics, cache clearing).

## Rules

- MUST use dedicated Event classes — NEVER string-based events (`event('car.created')`)
- Event payload properties MUST be `private readonly` — NEVER `public`
- MUST expose payload via public getter methods
- MUST dispatch events AFTER the main DB operation succeeds — never before
- NEVER dispatch events inside a `DB::transaction()` when the listener is queued (job may run before commit)
- Listeners that do any I/O (email, DB write, HTTP call) MUST implement `ShouldQueue`
- Listeners MUST have single responsibility — one side effect per listener

## Event Structure

```php
class CarCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private readonly Car $car,
    ) {}

    public function getCar(): Car
    {
        return $this->car;
    }
}
```

## Listener Structure

```php
class SendCarCreatedNotification implements ShouldQueue
{
    public function handle(CarCreated $event): void
    {
        Mail::to($event->getCar()->owner)->send(new CarCreatedMail($event->getCar()));
    }
}
```

## Dispatching

```php
$car = Car::create($data);
CarCreated::dispatch($car); // after the write, outside any transaction
```
