<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

/**
 * Represents a 'method not allowed' error. Thrown to indicate that the request
 * method is known by the server but is not supported by the target resource.
 */
class MethodNotAllowedException extends HttpException
{
    /**
     * Initializes the exception.
     *
     * @param string $msg The message to display.
     */
    public function __construct($msg = null)
    {
        parent::__construct($msg === null ? "Method not allowed." : $msg, 405);
    }
}
