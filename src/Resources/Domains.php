<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Domains extends Resource
{
    /** Adds a domain; the response lists the DNS records to add. @param array{name: string, region?: string, return_path?: string, click_tracking?: bool, open_tracking?: bool} $params */
    public function create(array $params): array
    {
        return $this->client->request('POST', '/domains', $params);
    }

    public function get(string $id): array
    {
        return $this->client->request('GET', '/domains/' . self::id($id));
    }

    public function list(): array
    {
        return $this->client->request('GET', '/domains');
    }

    /** Checks DNS now and returns the updated domain. */
    public function verify(string $id): array
    {
        return $this->client->request('POST', '/domains/' . self::id($id) . '/verify', new \stdClass());
    }

    /** @param array{click_tracking?: bool, open_tracking?: bool, tls?: string, return_path?: string} $params */
    public function update(string $id, array $params): array
    {
        return $this->client->request('PATCH', '/domains/' . self::id($id), $params);
    }

    public function remove(string $id): array
    {
        return $this->client->request('DELETE', '/domains/' . self::id($id));
    }
}
