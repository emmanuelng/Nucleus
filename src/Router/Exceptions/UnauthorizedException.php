<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

/**
 * Represents an 'unauthorized' error. Thrown to indicate that the request has
 * not been applied because it lacks valid authentication credentials for the
 * target resource.
 */
class UnauthorizedException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 401);
    }
}
