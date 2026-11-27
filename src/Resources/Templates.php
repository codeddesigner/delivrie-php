<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Templates extends Resource
{
    /** @param array{name: string, alias?: string, subject?: string, html?: string, from?: string, variables?: array} $params */
    public function create(array $params): array
    {
        return $this->client->request('POST', '/templates', $params);
    }

    /** By id or alias. */
    public function get(string $idOrAlias): array
    {
        return $this->client->request('GET', '/templates/' . self::id($idOrAlias));
    }

    public function list(): array
    {
        return $this->client->request('GET', '/templates');
    }

    /** Makes the current draft the version that sends use. */
    public function publish(string $id): array
    {
        return $this->client->request('POST', '/templates/' . self::id($id) . '/publish', new \stdClass());
    }
}
