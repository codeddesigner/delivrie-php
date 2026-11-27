<?php

declare(strict_types=1);

namespace Delivrie\Exception;

/** Thrown by Webhook::verify when a request didn't come from Delivrie, was changed, or is a replay. */
class WebhookVerificationException extends \RuntimeException
{
}
