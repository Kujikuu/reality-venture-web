<?php

namespace App\Exceptions;

use RuntimeException;

class DomeIntegrationException extends RuntimeException
{
    /** @param array<string, list<string>> $errors */
    public function __construct(public readonly string $reason, public readonly array $errors = [], public readonly ?int $status = null)
    {
        parent::__construct("The Dome request failed: {$reason}");
    }
}
