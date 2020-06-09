<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

/**
 * Exception thrown to indicate that an unknown schema field was referenced.
 */
class UnknownFieldException extends Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $fieldName The name of the referenced field.
     */
    public function __construct(string $fieldName)
    {
        parent::__construct("Unknown field '$fieldName'.");
    }
}