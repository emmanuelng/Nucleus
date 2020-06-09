<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

/**
 * Represents a 'not found' error. Thrown to indicate that the server can't
 * find the requested resource.
 */
class NotFoundException extends HttpException
{
    /**
     * Initializes the exception.
     *
     * @param string $message The message to display.
     */
    public function __construct(string $msg = null)
    {
        parent::__construct($msg === null ? "Not found." : $msg, 404);
    }
}
