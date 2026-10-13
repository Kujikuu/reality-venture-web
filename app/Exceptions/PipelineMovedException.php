<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Something tried to change an application after the pipeline moved to
 * Sniper Hub. Every such path should be closed; this is the backstop.
 */
class PipelineMovedException extends RuntimeException
{
    public static function forApplication(?string $uid): self
    {
        return new self('Applications moved to Sniper Hub and are read-only here'.($uid ? " ({$uid})." : '.'));
    }
}
