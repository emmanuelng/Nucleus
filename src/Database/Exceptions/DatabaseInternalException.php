<?php

declare(strict_types=1);

namespace Nucleus\Database\Exceptions;

use Exception;

class DatabaseInternalException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}