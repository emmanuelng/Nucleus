<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

use Exception;

/**
 * This class is used as a marker class to indicate that an exception is an HTTP
 * exception. Such exceptions have error codes that correspond to a valid HTTP
 * response code (e.g. NotFound -> 404). In the router, all exceptions that don't
 * extend this class are considered as server internal errors.
 */
abstract class HttpException extends Exception
{ }
