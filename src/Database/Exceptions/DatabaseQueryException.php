<?php

declare(strict_types=1);

namespace Nucleus\Database\Exceptions;

use Exception;

/**
 * Exception thrown to indicate that an error occurred while executing a query.
 */
class DatabaseQueryException extends Exception
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
