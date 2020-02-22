<?php

declare(strict_types=1);

namespace Nucleus\Router\Resolvers;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Exceptions\MethodNotAllowedException;
use Nucleus\Router\Exceptions\NotFoundException;
use Nucleus\Router\Resolver;
use Nucleus\Router\Route;
use Nucleus\Router\Routes\ResolvedRoute;

/**
 * A resolver that uses regular expressions to resolve URLs.
 * This resolver uses the 'first-match' policy. Therefore the order in which
 * the routes are registered matters.
 */
class RegexResolver implements Resolver
{
    /**
     * List of regular expressions matching the registered routes' URLs.
     *
     * @var array
     */
    private $regex;

    /**
     * A map from URL regular expression to their corresponding route
     * object.
     *
     * @var array
     */
    private $routes;

    /**
     * The parameter positions for each registered URL.
     *
     * @var array
     */
    private $params;

    /**
     * Initializes the resolver.
     */
    public function __construct()
    {
        $this->regex  = [];
        $this->routes = [];
        $this->params = [];
    }

    /**
     * {@inheritDoc}
     */
    public function register(Route $route): void
    {
        // Get the route information
        $method = strtoupper($route->method());
        $url    = trim($route->url(), '/');

        // Get the parameters schema
        $paramSchema = $route->parameters();

        // Build the route's regular expression
        $parts    = explode('/', $url);
        $regex    = '';
        $curPos   = 0;
        $params   = [];

        foreach ($parts as $part) {
            if (preg_match('/:(.)+/', $part)) {
                // The current part is a parameter.
                $paramName = substr($part, 1);
                if ($paramSchema === null) {
                    $msg = "Undefined parameter $paramName";
                    throw new InvalidRouteException($msg);
                }

                $paramObj = $paramSchema->getField($paramName);
                if ($paramSchema === null) {
                    $msg = "Undefined parameter $paramName";
                    throw new InvalidRouteException($msg);
                }

                $regex .= $paramObj->isOptional() ? '*\/' : "(.)+\/";
                $params[$paramName] = $curPos;
            } else {
                // The current part isn't a parameter.
                $regex  .= "$part\/";
            }

            $curPos++;
        }

        $regex = '/' . substr($regex, 0, -2) . '/';

        // Get the route's method
        $method = strtoupper($route->method());

        // Check for duplicate URLs.
        if (isset($this->routes[$regex][$method])) {
            $msg = "The route $method:$url is already defined.";
            throw new InvalidRouteException($msg);
        }

        // Register the route
        $this->regex[] = $regex;
        $this->params[$regex] = $params;
        $this->routes[$regex][$method] = $route;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(string $method, string $url): ResolvedRoute
    {
        // Pre-process the inputs.
        $method = strtoupper($method);
        $url    = trim($url, '/');

        // Determine whether the URL exists.
        $found = false;
        $regex = '';

        foreach ($this->regex as $curRegex) {
            if (preg_match($curRegex, $url)) {
                $regex = $curRegex;
                $found = true;
                break;
            }
        }

        // Not found.
        if (!$found) {
            throw new NotFoundException($method, $url);
        }

        // Check whether the request method is allowed
        $route = $this->routes[$regex][$method] ?? null;
        if ($route === null) {
            throw new MethodNotAllowedException($method, $url);
        }

        // Extract the parameters.
        $parts     = explode('/', $url);
        $urlParams = [];

        foreach ($this->params[$regex] as $name => $position) {
            $urlParams[$name] = $parts[$position] ?? null;
        }

        // Return the resolved route.
        return new ResolvedRoute($route, $urlParams);
    }
}
