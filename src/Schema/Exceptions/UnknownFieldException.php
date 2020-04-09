<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

class UnknownFieldException extends Exception
{
    public function __construct(string $fieldName)
    {
        parent::__construct("Unknown field '$fieldName'.");
    }
}