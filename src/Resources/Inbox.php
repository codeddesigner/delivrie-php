<?php

declare(strict_types=1);

namespace Delivrie\Resources;

final class Inbox extends Resource
{
    /** Conversations. @param array{limit?: int, offset?: int, needs_reply?: bool, status?: string} $params */
    public function threads(array $params = []): array
    {
        if (isset($params['needs_reply']) && is_bool($params['needs_reply'])) {
            $params['needs_reply'] = $params['needs_reply'] ? 'true' : 'false';
        }
        return $this->client->request('GET', '/inbox/threads', null, ['query' => $params]);
    }

    public function thread(string $id): array
    {
        return $this->client->request('GET', '/inbox/threads/' . self::id($id));
    }

    /** Changes a conversation's status, labels or assignee. */
    public function updateThread(string $id, array $changes): array
    {
        return $this->client->request('PATCH', '/inbox/threads/' . self::id($id), $changes);
    }

    /** Answers a conversation. @param array{html?: string, text?: string} $reply */
    public function reply(string $id, array $reply): array
    {
        return $this->client->request('POST', '/inbox/threads/' . self::id($id) . '/reply', $reply);
    }

    public function labels(): array
    {
        return $this->client->request('GET', '/inbox/labels');
    }
}
