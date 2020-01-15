<?php

declare(strict_types=1);

namespace Nucleus\Router;

use Nucleus\Types\Schema;

/**
 * Represents a route.
 */
interface Route
{
    /**
     * Returns the route's request method.
     *
     * @return string The request method.
     */
    public function method(): string;

    /**
     * Returns the route's URL.
     *
     * @return string The URL.
     */
    public function url(): string;

    /**
     * Returns the schema of the route's parameters.
     *
     * @return Schema The parameter's schema.
     */
    public function parameters(): Schema;

    /**
     * Returns the schema of the request body.
     *
     * @return Schema The request body schema.
     */
    public function requestBody(): Schema;

    /**
     * Returns the schema of the response body.
     *
     * @return Schema The response body schema.
     */
    public function responseBody(): Schema;

    /**
     * Executes the route.
     *
     * @param Request $req The request.
     * @param Response $res The response.
     * @return void
     */
    public function execute(Request $req, Response $res): void;
}
