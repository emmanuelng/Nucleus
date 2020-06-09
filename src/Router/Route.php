<?php

declare(strict_types=1);

namespace Nucleus\Router;

use Nucleus\Schema\Schema;

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
     * Returns the schema of the parameters of the route.
     *
     * @return Schema|null The parameter's schema.
     */
    public function parameters(): ?Schema;

    /**
     * Returns the schema of the request body.
     *
     * @return Schema|null The request body schema.
     */
    public function requestBody(): ?Schema;

    /**
     * Executes the route.
     *
     * @param Request  $req The request.
     * @param Response $res The response.
     *
     * @return void
     */
    public function execute(Request $req, Response $res): void;
}
