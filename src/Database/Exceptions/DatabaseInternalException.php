<?php

declare(strict_types=1);

namespace Nucleus\Database\Exceptions;

use Exception;

/**
 * Exception thrown to indicate an internal database error. These errors are
 * code- or configuration-related and are independent of the queries or
 * migrations that are executed.
 */
class DatabaseInternalException extends Exception
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
