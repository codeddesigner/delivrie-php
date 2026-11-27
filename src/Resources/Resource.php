<?php

declare(strict_types=1);

namespace Delivrie\Resources;

use Delivrie\Delivrie;

/** @internal */
abstract class Resource
{
    public function __construct(protected readonly Delivrie $client)
    {
    }

    protected static function id(string $id): string
    {
        return rawurlencode($id);
    }

    /** @param array{limit?: int, offset?: int} $params */
    protected static function page(array $params): array
    {
        return ['query' => $params];
    }
}
