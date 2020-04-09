<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

class InvalidSchemaFileException extends Exception
{
    public function __construct(string $path)
    {
        parent::__construct("Parsing error: $path isn't a valid schema file.");
    }
}