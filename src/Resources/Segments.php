<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Segments extends Resource
{
    public function create(string $name): array
    {
        return $this->client->request('POST', '/segments', ['name' => $name]);
    }

    public function list(): array
    {
        return $this->client->request('GET', '/segments');
    }

    public function remove(string $id): array
    {
        return $this->client->request('DELETE', '/segments/' . self::id($id));
    }

    /** Adds contacts by email address (or full contact arrays); they're created if needed. */
    public function addContacts(string $id, array $contacts): array
    {
        $list = array_map(static fn ($c) => is_string($c) ? ['email' => $c] : $c, array_values($contacts));
        return $this->client->request('POST', '/segments/' . self::id($id) . '/contacts', ['contacts' => $list]);
    }

    /** Subscription topics. */
    public function topics(): array
    {
        return $this->client->request('GET', '/topics');
    }
}
