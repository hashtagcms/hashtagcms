# External Log Export (Graylog / Last9 / OTLP)

HashtagCMS can ship application log entries — the same ones written to
`laravel.log` — to an external observability backend such as **Graylog**
or **Last9** (or any other OTLP/HTTP-logs compatible system). This is
entirely config-driven and **off by default**: installs that don't set
`CMS_LOG_EXPORT_ENABLED` see zero behavior change and zero overhead.

---

## How it works

Laravel dispatches an internal `Illuminate\Log\Events\MessageLogged` event
for every `Log::info()` / `Log::error()` / `Log::warning()` / etc. call,
regardless of which channel it's written to. HashtagCMS listens for that
event and, when enabled, forwards matching entries to the configured
backend — **no changes to your app's own `config/logging.php` are
required.**

Delivery is queued by default (`CMS_LOG_EXPORT_ASYNC=true`), so a slow or
unreachable log backend never adds latency to the request that triggered
the log line. A backend failure is swallowed silently — it will never
break your app or generate more log entries that loop back into the
export pipeline.

> **This only ships asynchronously if `QUEUE_CONNECTION` is a real queue**
> (`redis`, `database`, `sqs`, ...) with a worker running. If it's set to
> `sync` (common in local/dev setups), Laravel executes the queued job
> immediately and inline — so the network call to Graylog/Last9 *will*
> block the request, regardless of `CMS_LOG_EXPORT_ASYNC`. Check
> `QUEUE_CONNECTION` in your `.env` before relying on this in production.

---

## Enabling it

Publish the config once if you haven't already:

```bash
php artisan vendor:publish --tag=hashtagcms.config
```

This adds `config/hashtagcmslog.php` to your app, backed by the env vars
below.

### Common settings

| Env var | Default | Description |
|---|---|---|
| `CMS_LOG_EXPORT_ENABLED` | `false` | Master switch. |
| `CMS_LOG_EXPORT_DRIVER` | `none` | `none` \| `graylog` \| `last9` |
| `CMS_LOG_EXPORT_MIN_LEVEL` | `error` | Minimum PSR-3 level shipped externally (`debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, `emergency`). Everything still writes to `laravel.log` regardless of this setting — it only filters what leaves the app. |
| `CMS_LOG_EXPORT_ASYNC` | `true` | Ship via a queued job instead of inline. |
| `CMS_LOG_EXPORT_QUEUE` | `default` | Queue name used for the shipping job. |

Sensitive context keys (`password`, `token`, `secret`, `api_key`,
`authorization`, `access_token`, `refresh_token`) are stripped before an
entry ever leaves the app. Add more via the `redact_keys` array in
`config/hashtagcmslog.php`.

---

## Graylog

Ships entries as GELF over TCP or UDP.

```env
CMS_LOG_EXPORT_ENABLED=true
CMS_LOG_EXPORT_DRIVER=graylog

GRAYLOG_HOST=graylog.internal.example.com
GRAYLOG_PORT=12201
GRAYLOG_PROTOCOL=tcp   # tcp (default) or udp
```

Requires the `graylog2/gelf-php` composer package, which ships as a
dependency of `hashtagcms/hashtagcms`.

---

## Last9

Ships entries as an OpenTelemetry (OTLP) logs payload over HTTP/JSON.
Because this is the vendor-neutral OTLP format, the same driver works for
any other OTLP-compatible ingestion endpoint (Grafana Cloud, Honeycomb,
an OTel Collector, etc.) — just point `LAST9_OTLP_ENDPOINT` elsewhere.

```env
CMS_LOG_EXPORT_ENABLED=true
CMS_LOG_EXPORT_DRIVER=last9

LAST9_OTLP_ENDPOINT=https://otlp.last9.io/v1/logs
LAST9_AUTH_HEADER=Authorization
LAST9_AUTH_VALUE="Basic base64(user:pass)"   # or "Bearer <token>" per your Last9 credentials
LAST9_TIMEOUT=2
```

Get the exact endpoint and auth header format from your Last9
project's ingestion settings.

---

## Adding another backend

Implement `HashtagCms\Core\Logging\LogExporter`:

```php
class DatadogExporter implements LogExporter
{
    public function send(array $entry): void
    {
        // $entry: level, message, context, timestamp, app, env, hostname
    }
}
```

Then add a case for it in `HashtagCms\Core\Logging\LogExportManager::driver()`
and a `CMS_LOG_EXPORT_DRIVER=datadog` value. Nothing else in the pipeline
(listener, queue job, redaction, level filtering) needs to change.

---

**Previous:** [API Caching](38-caching.md) · **Index:** [Documentation Home](00-index.md)
