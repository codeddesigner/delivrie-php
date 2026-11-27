<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Suppressions extends Resource
{
    /** Stops sending to one address or a list of them. */
    public function add(string|array $email): array
    {
        return $this->client->request('POST', '/suppressions', is_array($email) ? ['emails' => array_values($email)] : ['email' => $email]);
    }

    public function list(): array
    {
        return $this->client->request('GET', '/suppressions');
    }

    /** Lets an address receive email again. */
    public function remove(string $email): array
    {
        return $this->client->request('DELETE', '/suppressions/' . self::id($email));
    }
}
