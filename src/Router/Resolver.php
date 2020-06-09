<?php

declare(strict_types=1);

namespace Nucleus\Router;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Exceptions\MethodNotAllowedException;
use Nucleus\Router\Exceptions\NotFoundException;
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
     *
     * @return void
     *
     * @throws InvalidRouteException If the route isn't correctly configured.
     */
    public function register(Route $route): void;

    /**
     * Resolves a URL.
     *
     * @param string $method The request method.
     * @param string $url    The input URL.
     *
     * @return ResolvedRoute The corresponding route.
     *
     * @throws NotFoundException If no route is associated to the requested
     * URL.
     *
     * @throws MethodNotAllowedException If the URL exists, but not with the
     * request method.
     */
    public function resolve(string $method, string $url): ResolvedRoute;
}
