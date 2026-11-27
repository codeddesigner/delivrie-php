<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Automations extends Resource
{
    public function list(): array
    {
        return $this->client->request('GET', '/automations');
    }

    public function get(string $id): array
    {
        return $this->client->request('GET', '/automations/' . self::id($id));
    }

    public function create(array $definition): array
    {
        return $this->client->request('POST', '/automations', $definition);
    }

    public function runs(string $id): array
    {
        return $this->client->request('GET', '/automations/' . self::id($id) . '/runs');
    }

    public function publish(string $id): array
    {
        return $this->client->request('POST', '/automations/' . self::id($id) . '/publish', new \stdClass());
    }

    public function pause(string $id): array
    {
        return $this->client->request('POST', '/automations/' . self::id($id) . '/pause', new \stdClass());
    }

    public function resume(string $id): array
    {
        return $this->client->request('POST', '/automations/' . self::id($id) . '/resume', new \stdClass());
    }
}
