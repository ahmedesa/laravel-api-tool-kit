# Services (External Integrations Only)

Services wrap **external 3rd-party APIs** (payment gateways, SMS providers, external data sync). They are NOT for internal business logic — use Actions for that.

Each service is **self-contained**: its own directory with Interface, Concrete, Mock, and ServiceProvider.

## Rules

- MUST have its own `ServiceProvider` — NEVER bind in `AppServiceProvider`
- MUST define an Interface for the contract
- MUST use the `ConsumesExternalServices` trait from the package for HTTP calls
- MUST provide a Mock implementation for the `testing` environment
- MUST read config values in the ServiceProvider and pass to the constructor — NEVER call `config()` inside the class
- MUST implement `resolveAuthorization()` to inject auth headers/params
- MUST throw exceptions on failure — NEVER return HTTP responses
- NEVER put internal domain workflows in a Service — use Actions
- NEVER use the `new` keyword to instantiate a Service — always use constructor injection (via Interface)

## Directory Structure

```
app/Services/SmsProvider/
├── SmsProviderInterface.php        # Contract — public methods only
├── SmsProvider.php                 # Concrete — uses ConsumesExternalServices
├── SmsProviderMock.php             # Mock — returns fake data, no HTTP calls
└── SmsProviderServiceProvider.php  # Binds interface → concrete or mock
```

## Interface

```php
interface SmsProviderInterface
{
    public function send(string $to, string $message): array;
}
```

## Concrete (uses ConsumesExternalServices)

```php
use Essa\APIToolKit\Api\ConsumesExternalServices;
use Illuminate\Http\Client\PendingRequest;

class SmsProvider implements SmsProviderInterface
{
    use ConsumesExternalServices;

    public function __construct(
        protected string $baseUri,
        protected string $apiKey,
    ) {}

    // Called automatically by ConsumesExternalServices before every request.
    // Receives the PendingRequest instance — add auth headers and return it.
    public function resolveAuthorization(PendingRequest $request): PendingRequest
    {
        return $request->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ]);
    }

    public function send(string $to, string $message): array
    {
        // makeRequest signature: (string $method, string $requestUrl, array $data, array $headers, bool $isJsonRequest)
        // Returns decoded JSON by default, or throws on failure (override handleRequestError() to customize).
        return $this->makeRequest(
            method: 'POST',
            requestUrl: '/messages',
            data: ['to' => $to, 'body' => $message],
        );
    }
}
```

## Mock

```php
class SmsProviderMock implements SmsProviderInterface
{
    public function send(string $to, string $message): array
    {
        return ['status' => 'sent', 'message_id' => 'fake-id-123'];
    }
}
```

## ServiceProvider (self-contained binding)

```php
class SmsProviderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsProvider::class, fn(): SmsProvider => new SmsProvider(
            baseUri: config('services.sms.base_uri'),
            apiKey: config('services.sms.api_key'),
        ));

        $target = match ($this->app->environment()) {
            'testing' => SmsProviderMock::class,
            default   => SmsProvider::class,
        };

        $this->app->bind(SmsProviderInterface::class, $target);
    }
}
```

Register in `bootstrap/providers.php`:

```php
return [
    SmsProviderServiceProvider::class,
];
```
