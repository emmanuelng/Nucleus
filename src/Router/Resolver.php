<?php

declare(strict_types=1);

namespace Nucleus\Router;

use Nucleus\Router\Routes\ResolvedRoute;

/**
 * Interface that must be suported by classes that are used to resolve URLs
 * received by the router.
 */
interface Resolver
{
    /**
     * Registers a route into the resolver.
     *
     * @param Route $route The route.
     * @return void
     */
    public function register(Route $route): void;

    /**
     * Resolves a URL. Must throw an exception if the route cannot be resolved.
     *
     * @param string $method The request method.
     * @param string $url The input URL
     * @return ResolvedRoute The corresponding route.
     */
    public function resolve(string $method, string $url): ResolvedRoute;
}
