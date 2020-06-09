<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

/**
 * Exception thrown to indicate that an undefiend type was referenced.
 */
class UnknownTypeException extends Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $typeName The name of the referenced type.
     */
    public function __construct(string $typeName)
    {
        parent::__construct("Unknown type '$typeName'.");
    }
}