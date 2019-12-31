<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

use Exception;

class InvalidRouteException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
