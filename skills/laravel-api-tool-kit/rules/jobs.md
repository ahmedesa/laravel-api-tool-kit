# Jobs (Standalone Queued Jobs)

Use a standalone Job when work must happen asynchronously but is NOT a side effect of a domain event. If the async work IS triggered by something that happened (order placed, user registered), use an Event + Listener instead — see `rules/events.md`.

## When to Use a Job vs Event Listener

| Scenario | Use |
|----------|-----|
| Side effect of something that happened (send email after order) | Event + Listener |
| Scheduled / periodic work (generate reports, clean up) | Job (dispatched from scheduler) |
| User-initiated heavy work (export CSV, process upload) | Job (dispatched from controller/action) |
| Retry-sensitive operation (payment capture, webhook delivery) | Job |

## Rules

- MUST implement `ShouldQueue` — a Job class without `ShouldQueue` runs synchronously and defeats the purpose
- MUST use `private readonly` constructor promotion for all dependencies
- MUST pass only serializable data to the constructor — primitives, model IDs, or models (via `SerializesModels`)
- MUST be idempotent — running the same job twice with the same data MUST produce the same result
- MUST define `$tries`, `$backoff`, and `$timeout` — NEVER rely on defaults for production jobs
- MUST re-fetch mutable state inside `handle()` — NEVER trust data serialized at dispatch time for values that can change
- NEVER use `auth()->id()`, `request()`, or any HTTP-context helpers inside a job — they are always null in queue workers
- NEVER dispatch a job inside `DB::transaction()` — the job may execute before the transaction commits
- NEVER pass entire Request objects or closures to a job — they are not serializable

## Structure

```php
final class ProcessCsvImportJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 60, 300];
    public int $timeout = 120;

    public function __construct(
        private readonly User $user,
        private readonly string $filePath,
    ) {}

    public function handle(CsvImportAction $importer): void
    {
        $importer->execute($this->user, $this->filePath);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CSV import failed', [
            'user_id' => $this->user->id,
            'file' => $this->filePath,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

## Dispatching

```php
// From a controller — return 202 Accepted
public function import(ImportCsvRequest $request): JsonResponse
{
    $path = $request->file('csv')->store('imports');

    ProcessCsvImportJob::dispatch($request->user(), $path);

    return $this->responseAccepted(trans('import.queued'));
}

// From the scheduler — in routes/console.php or app/Console/Kernel.php
Schedule::job(new PruneExpiredTokensJob())->daily();
```

## Idempotency

Jobs can be retried on failure. Design for safe re-execution:

```php
// Wrong — creates duplicate records on retry
public function handle(): void
{
    Report::create(['user_id' => $this->userId, 'date' => today()]);
}

// Correct — upsert or check-before-write
public function handle(): void
{
    Report::updateOrCreate(
        ['user_id' => $this->userId, 'date' => today()],
        ['generated_at' => now()],
    );
}
```

## Unique Jobs

Prevent duplicate jobs from being queued for the same logical operation:

```php
use Illuminate\Contracts\Queue\ShouldBeUnique;

final class GenerateDailyReportJob implements ShouldQueue, ShouldBeUnique
{
    public int $uniqueFor = 3600; // seconds

    public function uniqueId(): string
    {
        return "daily-report:{$this->user->id}";
    }
}
```

## Job Batching

For operations that process many items and need progress tracking or all-or-nothing behavior:

```php
// Dispatch a batch from a controller or action
Bus::batch([
    new ProcessRowJob($row1),
    new ProcessRowJob($row2),
    new ProcessRowJob($row3),
])
    ->name("Import {$file->name}")
    ->onQueue('imports')
    ->dispatch();
```

## Queue Selection

Use named queues to separate job priorities:

```php
// High-priority — payment, auth
ProcessPaymentJob::dispatch($order)->onQueue('payments');

// Default — standard async work
ProcessCsvImportJob::dispatch($user, $path)->onQueue('default');

// Low-priority — reports, cleanup
GenerateMonthlyReportJob::dispatch()->onQueue('low');
```

## Common Pitfalls

| Pitfall | Why it breaks | Fix |
|---------|--------------|-----|
| `auth()->id()` in `handle()` | Always null in queue context | Pass user/ID via constructor |
| Dispatching inside `DB::transaction()` | Job runs before commit, reads missing data | Dispatch after transaction |
| No `$tries` defined | Infinite retries on transient failures | Always set `$tries` |
| Model deleted before job runs | `ModelNotFoundException` on deserialize | Use `DeleteWhenMissingModels` trait or handle gracefully |
| Large payload in constructor | Serialization bloat, memory issues | Pass IDs, re-fetch in `handle()` |
