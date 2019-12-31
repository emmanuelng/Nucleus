<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

class ForbiddenException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 403);
    }
}