<?php

declare(strict_types=1);

namespace Nucleus\Router\Routes;

use Nucleus\Router\Request;
use Nucleus\Router\Response;
use Nucleus\Router\Route;
use Nucleus\Schema\Schema;

/**
 * Represents a route resolved by a Resolver object. Contains the route to be
 * executed and additional information found by the resolver, such as URL
 * parameters.
 */
class ResolvedRoute implements Route
{
    /**
     * The route to execute.
     *
     * @var Route
     */
    private $route;

    /**
     * The URL parameters.
     *
     * @var array
     */
    private $urlParameters;

    /**
     * Initializes the resolved route.
     *
     * @param Route $route         The route to execute.
     * @param array $urlParameters The URL parameters.
     */
    public function __construct(Route $route, array $urlParameters = [])
    {
        $this->route         = $route;
        $this->urlParameters = $urlParameters;
    }

    /**
     * Returns the URL parameters.
     *
     * @return array The URL parameters.
     */
    public function urlParameters(): array
    {
        return $this->urlParameters;
    }

    /**
     * {@inheritDoc}
     */
    public function method(): string
    {
        return $this->route->method();
    }

    /**
     * {@inheritDoc}
     */
    public function url(): string
    {
        return $this->route->url();
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): ?Schema
    {
        return $this->route->parameters();
    }

    /**
     * {@inheritDoc}
     */
    public function requestBody(): ?Schema
    {
        return $this->route->requestBody();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $req, Response $res): void
    {
        $this->route->execute($req, $res);
    }
}
