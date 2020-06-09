<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

/**
 * Represents a 'bad request' error. Thrown to indicate that the server cannot
 * or will not process the request due to something that is perceived to be a
 * client error (e.g., malformed request syntax, invalid request message
 * framing, or deceptive request routing).
 */
class BadRequestException extends HttpException
{
    /**
     * Initializes the exception.
     *
     * @param string $msg The message to display.
     */
    public function __construct(string $msg = null)
    {
        parent::__construct($msg === null ? 'Bad request.' : $msg, 400);
    }
}