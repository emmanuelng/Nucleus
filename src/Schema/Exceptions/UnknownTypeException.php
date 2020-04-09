<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

class UnknownTypeException extends Exception
{
    public function __construct(string $typeName)
    {
        parent::__construct("Unknown field '$typeName'.");
    }
}