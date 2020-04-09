<?php

declare(strict_types=1);

namespace Nucleus\Router;

use Nucleus\Json\JsonObject;

/**
 * Represents a router's request.
 */
interface Request
{
    /**
     * Returns the request method.
     *
     * @return string The method
     */
    public function method(): string;

    /**
     * Returns the request URL.
     *
     * @return string The URL
     */
    public function url(): string;

    /**
     * Returns the request headers.
     *
     * @return array The headers
     */
    public function headers(): array;

    /**
     * Returns the request parameters.
     *
     * @return array The parameters
     */
    public function parameters(): array;

    /**
     * Returns the request body.
     *
     * @return array The request body
     */
    public function body(): JsonObject;
}
