<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Logs extends Resource
{
    /** Recent API requests. @param array{limit?: int, offset?: int} $params */
    public function list(array $params = []): array
    {
        return $this->client->request('GET', '/logs', null, self::page($params));
    }

    /** One request with its bodies. */
    public function get(string $id): array
    {
        return $this->client->request('GET', '/logs/' . self::id($id));
    }
}
