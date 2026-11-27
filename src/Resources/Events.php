<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Events extends Resource
{
    /** Records an event for a contact; live automations listening for it start. @param array{event: string, email: string, data?: array} $params */
    public function send(array $params): array
    {
        return $this->client->request('POST', '/events', $params);
    }

    public function list(): array
    {
        return $this->client->request('GET', '/events');
    }
}
