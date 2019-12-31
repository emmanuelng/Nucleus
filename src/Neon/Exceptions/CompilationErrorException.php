<?php

declare(strict_types=1);

namespace Nucleus\Neon\Exceptions;

use Exception;

class CompilationErrorException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
