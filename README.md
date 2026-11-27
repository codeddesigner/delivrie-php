# Delivrie PHP SDK

The PHP client for [Delivrie](https://delivrie.io): send transactional email, manage domains, API keys, webhooks,
contacts, broadcasts and templates, and verify webhook signatures. PHP 8.1+, with only the curl and json extensions.

```sh
composer require delivrie/delivrie-php
```

```php
$delivrie = new \Delivrie\Delivrie(getenv('DELIVRIE_API_KEY'));

$email = $delivrie->emails->send([
    'from' => 'Acme <hi@acme.com>',
    'to' => 'ada@example.com',
    'subject' => 'Your receipt',
    'html' => '<p>Thanks for your order.</p>',
], ['idempotency_key' => 'order-1042']);

echo $email['id'];
```

Every method returns the API's JSON as an array. Errors throw `Delivrie\Exception\DelivrieException`:

```php
try {
    $delivrie->emails->send([...]);
} catch (\Delivrie\Exception\DelivrieException $e) {
    $e->getStatusCode(); // 422
    $e->getErrorName();  // "validation_error"
    $e->getMessage();    // "Provide `html` or `text`"
}
```

## What's included

| Resource | Methods |
| --- | --- |
| `emails` | `send`, `sendBatch` (up to 100), `get`, `list`, `listReceived`, `reschedule`, `cancel`, `forward` |
| `domains` | `create`, `get`, `list`, `verify`, `update`, `remove` |
| `apiKeys` | `create`, `list`, `remove` |
| `webhooks` | `create`, `list`, `deliveries`, `test`, `replay`, `verify` |
| `contacts`, `segments` | `create`, `get`, `update`, `list`, `remove`; `addContacts`, `topics` |
| `broadcasts` | `create`, `get`, `list`, `send` (now or scheduled), `cancel`, `recipients`, `remove` |
| `templates` | `create`, `get` (id or alias), `list`, `publish` |
| `suppressions`, `events`, `automations`, `inbox`, `logs` | as in the [API reference](https://delivrie.io/docs/api) |
| `usage()`, `metrics()` | plan usage and sending metrics |

`$delivrie->request('GET', '/path')` calls any endpoint the SDK doesn't wrap yet.

Templates, attachments and scheduling:

```php
$delivrie->emails->send([
    'to' => 'ada@example.com',
    'template' => ['id' => 'welcome', 'variables' => ['name' => 'Ada']],
    'attachments' => [['path' => __DIR__ . '/invoice.pdf']],   // or ['filename' => ..., 'content' => base64]
    'scheduled_at' => 'tomorrow at 9am',                        // or a DateTimeInterface
]);
```

## Webhooks

```php
$event = \Delivrie\Webhook::verify(
    file_get_contents('php://input'),   // the raw body, before decoding
    getallheaders(),
    getenv('DELIVRIE_WEBHOOK_SECRET'),  // whsec_…
);
// $event['type'] is "email.delivered", "email.bounced", …
```

It throws `Delivrie\Exception\WebhookVerificationException` for a bad signature or a replay older than 5 minutes.
In Laravel: `Webhook::verify($request->getContent(), $request->headers->all(), config('services.delivrie.webhook_secret'))`.

## Laravel

Put the key in `.env` as `DELIVRIE_API_KEY` and bind the client once:

```php
// app/Providers/AppServiceProvider.php
$this->app->singleton(\Delivrie\Delivrie::class, fn () => new \Delivrie\Delivrie(env('DELIVRIE_API_KEY')));
```

To send Laravel `Mailable`s through Delivrie without code changes, use the SMTP relay: see
[PHP and Laravel](https://delivrie.io/docs/php).

## Retries and options

Rate limits (429), server errors and network errors are retried twice with backoff, honouring `Retry-After`.
GET and DELETE are always retried; sends only with an `idempotency_key`, so a retry never sends twice.

```php
new \Delivrie\Delivrie($key, ['timeout' => 15, 'max_retries' => 3, 'base_url' => 'https://delivrie.io/api']);
```

A missing key falls back to `DELIVRIE_API_KEY`. Docs: https://delivrie.io/docs/php
