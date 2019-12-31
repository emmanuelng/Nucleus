<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

class MethodNotAllowedException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 405);
    }
}
