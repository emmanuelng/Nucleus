<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

/**
 * Exception thrown to indicate that a schema is not correctly configured.
 */
class InvalidSchemaException extends Exception
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