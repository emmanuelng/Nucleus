<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

use Exception;

/**
 * Exception thrown to indicate that a router's policy is incorrectly
 * configured.
 */
class InvalidPolicyException extends Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $message The message to display.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
