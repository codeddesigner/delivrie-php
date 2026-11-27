<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Contacts extends Resource
{
    /** @param array{email: string, first_name?: string, last_name?: string, unsubscribed?: bool, properties?: array, segments?: string[]} $params */
    public function create(array $params): array
    {
        return $this->client->request('POST', '/contacts', $params);
    }

    /** By id or email address. */
    public function get(string $idOrEmail): array
    {
        return $this->client->request('GET', '/contacts/' . self::id($idOrEmail));
    }

    /** By id or email address. */
    public function update(string $idOrEmail, array $params): array
    {
        return $this->client->request('PATCH', '/contacts/' . self::id($idOrEmail), $params);
    }

    /** @param array{limit?: int, offset?: int, segment_id?: string} $params */
    public function list(array $params = []): array
    {
        return $this->client->request('GET', '/contacts', null, ['query' => $params]);
    }

    /** By id or email address. */
    public function remove(string $idOrEmail): array
    {
        return $this->client->request('DELETE', '/contacts/' . self::id($idOrEmail));
    }
}
