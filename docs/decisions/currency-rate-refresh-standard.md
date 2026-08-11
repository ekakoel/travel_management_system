# Currency Rate Refresh Standard

Status: active
Updated: 2026-08-12

## Scope

This standard governs the `usd_rates` reference values used to convert USD,
CNY, and TWD to IDR. `sell` remains the authoritative pricing side; `buy` is
calculated as `sell - difference`, with a minimum of zero.

## Automatic Refresh

The canonical refresh command is:

```text
php artisan currency:refresh-rates
```

Laravel schedules this command once per day at `00:00` using
`config('app.timezone')` (`Asia/Singapore` by default), with overlap protection.
The scheduler invokes the command synchronously, so currency refresh does not
depend on a queue worker.

Production infrastructure must still invoke Laravel's scheduler every minute:

```cron
* * * * * cd /absolute/path/to/balikamitour && php artisan schedule:run >> /dev/null 2>&1
```

Registering an event in `app/Console/Kernel.php` does not start the operating
system scheduler. After deployment, verify with `php artisan schedule:list`
and monitor the application log for `Currency rates refreshed` or
`Scheduled currency refresh failed`.

## Provider and Calculation

- Credentials belong in `EXCHANGE_RATE_API_KEY`; no API key is committed as a
  configuration fallback.
- The provider endpoint is configured through `config/services.php`.
- The base currency is USD. Provider `conversion_rates` values are converted
  into IDR per unit: USD=`IDR`, CNY=`IDR/CNY`, TWD=`IDR/TWD`.
- HTTP calls use a bounded timeout and retry policy.
- `result=success` and positive USD, IDR, CNY, and TWD values are required.
- All three calculated rates must be valid before any database write begins.
- All database updates occur in one transaction and preserve each currency's
  configured `difference`.
- Duplicate rows for a currency fail closed; production data is not silently
  merged or deleted.
- Successful updates clear all known currency/pricing caches.

The response format follows the provider's official Standard Request contract:
`https://www.exchangerate-api.com/docs/standard-requests`.

## Manual Operations

Authorized administrators may:

- refresh all three currencies immediately from the market reference using
  the Currency page action; or
- manually update `sell` and `difference` for one currency.

Manual update verifies that the route currency matches the selected database
record, calculates `buy` server-side, writes the rate and audit log in one
transaction, marks `retrieval_source=manual-admin`, and clears the same caches.
The next successful midnight refresh replaces market values while preserving
the manually configured spread.

When the provider fails or returns incomplete data, existing stored rates stay
unchanged and pricing continues to use the last valid local data subject to the
pricing freshness contract.
