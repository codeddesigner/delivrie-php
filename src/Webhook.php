<?php

declare(strict_types=1);

namespace Delivrie;

use Delivrie\Exception\WebhookVerificationException;

/**
 * Verifies webhook requests. Delivrie signs with the Standard Webhooks scheme: webhook-id, webhook-timestamp and
 * webhook-signature headers, the signature being "v1,<base64 HMAC-SHA256 of id.timestamp.body>" keyed with the
 * endpoint's whsec_ signing secret.
 *
 *     $event = \Delivrie\Webhook::verify(file_get_contents('php://input'), getallheaders(), getenv('DELIVRIE_WEBHOOK_SECRET'));
 */
final class Webhook
{
    /**
     * Returns the parsed event, or throws WebhookVerificationException.
     *
     * @param string $payload The raw request body, exactly as received.
     * @param array<string, string|string[]> $headers Request headers (any letter case), e.g. getallheaders() or $request->headers->all().
     * @param string $secret The endpoint's signing secret, starting with whsec_.
     * @param int $toleranceSeconds Reject timestamps further than this from now. Default 300.
     */
    public static function verify(string $payload, array $headers, string $secret, int $toleranceSeconds = 300, ?int $now = null): array
    {
        if ($secret === '') {
            throw new WebhookVerificationException('Missing signing secret');
        }
        $h = [];
        foreach ($headers as $k => $v) {
            $h[strtolower((string) $k)] = is_array($v) ? (string) reset($v) : (string) $v;
        }
        $id = $h['webhook-id'] ?? '';
        $ts = $h['webhook-timestamp'] ?? '';
        $signatures = $h['webhook-signature'] ?? '';
        if ($id === '' || $ts === '' || $signatures === '') {
            throw new WebhookVerificationException('Missing webhook-id, webhook-timestamp or webhook-signature header');
        }
        if (!ctype_digit($ts)) {
            throw new WebhookVerificationException('Invalid webhook-timestamp header');
        }
        if (abs(($now ?? time()) - (int) $ts) > $toleranceSeconds) {
            throw new WebhookVerificationException('Webhook timestamp is too old or in the future');
        }
        $key = base64_decode(str_starts_with($secret, 'whsec_') ? substr($secret, 6) : $secret, true);
        if ($key === false) {
            throw new WebhookVerificationException("Signing secret isn't valid base64");
        }
        $expected = hash_hmac('sha256', "{$id}.{$ts}.{$payload}", $key, true);
        // Several space-separated signatures can arrive during secret rotation; any valid one is enough.
        $ok = false;
        foreach (preg_split('/\s+/', trim($signatures)) as $part) {
            [$version, $sig] = array_pad(explode(',', $part, 2), 2, '');
            $decoded = base64_decode($sig, true);
            if ($version === 'v1' && $decoded !== false && hash_equals($expected, $decoded)) {
                $ok = true;
                break;
            }
        }
        if (!$ok) {
            throw new WebhookVerificationException("Signature doesn't match");
        }
        $event = json_decode($payload, true);
        if (!is_array($event)) {
            throw new WebhookVerificationException('Body is not valid JSON');
        }
        return $event;
    }
}
