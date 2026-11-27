<?php

declare(strict_types=1);

namespace Delivrie\Exception;

/**
 * Thrown when the API returns an error, or the request couldn't be made. getStatusCode() is 0 for network errors
 * and timeouts; getErrorName() is Delivrie's error name, like "validation_error" or "rate_limit_exceeded".
 */
class DelivrieException extends \RuntimeException
{
    public function __construct(string $message, private readonly int $statusCode = 0, private readonly string $errorName = 'application_error')
    {
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorName(): string
    {
        return $this->errorName;
    }

    /** @internal */
    public static function fromResponse(int $status, mixed $json, string $raw): self
    {
        $j = is_array($json) ? $json : [];
        $name = is_string($j['name'] ?? null) ? $j['name'] : match ($status) {
            401 => 'invalid_api_key',
            404 => 'not_found',
            429 => 'rate_limit_exceeded',
            default => 'application_error',
        };
        $message = is_string($j['message'] ?? null) ? $j['message'] : (trim(substr($raw, 0, 300)) ?: "Request failed with status {$status}");
        return new self($message, is_int($j['statusCode'] ?? null) ? $j['statusCode'] : $status, $name);
    }
}
