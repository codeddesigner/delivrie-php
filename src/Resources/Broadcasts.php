<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Broadcasts extends Resource
{
    /** @param array{segment_id: string, from: string, subject: string, html?: string, text?: string, name?: string, reply_to?: string, topic_id?: string} $params */
    public function create(array $params): array
    {
        return $this->client->request('POST', '/broadcasts', $params);
    }

    public function get(string $id): array
    {
        return $this->client->request('GET', '/broadcasts/' . self::id($id));
    }

    public function list(): array
    {
        return $this->client->request('GET', '/broadcasts');
    }

    /** Sends now, or at $scheduledAt (ISO 8601, DateTimeInterface or a phrase like "tomorrow at 9am"). */
    public function send(string $id, string|\DateTimeInterface|null $scheduledAt = null): array
    {
        $body = $scheduledAt === null ? new \stdClass() : ['scheduled_at' => $scheduledAt instanceof \DateTimeInterface ? $scheduledAt->format(\DateTimeInterface::ATOM) : $scheduledAt];
        return $this->client->request('POST', '/broadcasts/' . self::id($id) . '/send', $body);
    }

    public function cancel(string $id): array
    {
        return $this->client->request('POST', '/broadcasts/' . self::id($id) . '/cancel', new \stdClass());
    }

    /** How many contacts the broadcast will reach. */
    public function recipients(string $id): array
    {
        return $this->client->request('GET', '/broadcasts/' . self::id($id) . '/recipients');
    }

    public function remove(string $id): array
    {
        return $this->client->request('DELETE', '/broadcasts/' . self::id($id));
    }
}
