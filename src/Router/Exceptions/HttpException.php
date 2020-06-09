<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

use Exception;

/**
 * This class is used to indicate that an exception is an HTTP exception. These
 * exceptions have error codes corresponding to valid HTTP response codes (e.g.
 * NotFound -> 404). In the router, all exceptions that do not extend this
 * class are considered internal server errors.
 */
abstract class HttpException extends Exception
{
}
