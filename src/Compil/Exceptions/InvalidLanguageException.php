<?php

declare(strict_types=1);

namespace Nucleus\Compil\Exceptions;

use Exception;

class InvalidLanguageException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
