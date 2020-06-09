<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

/**
 * Represents a 'forbidden' error. Thrown to indicate that the server
 * understood the request but refuses to authorize it.
 */
class ForbiddenException extends HttpException
{
    /**
     * Initializes the exception.
     *
     * @param string $msg The message to display.
     */
    public function __construct(string $msg = null)
    {
        parent::__construct($msg === null ? 'Forbidden.' : $msg, 403);
    }
}
