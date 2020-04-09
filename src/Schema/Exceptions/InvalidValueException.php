<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

class InvalidValueException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}