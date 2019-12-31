<?php

declare(strict_types=1);

namespace Nucleus\Router\Routes;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Request;
use Nucleus\Router\Response;
use Nucleus\Router\Route;

/**
 * Concrete class of the Route interface allowing to define routes using
 * arrays.
 */
class ArrayRoute implements Route
{
    /**
     * The request method.
     *
     * @var string
     */
    private $method;

    /**
     * The URL.
     *
     * @var string
     */
    private $url;

    /**
     * The parameters schema.
     *
     * @var array
     */
    private $parameters;

    /**
     * The request schema.
     *
     * @var array
     */
    private $requestBody;

    /**
     * The response schema.
     *
     * @var array
     */
    private $responseBody;

    /**
     * The route's callback.
     *
     * @var callable
     */
    private $callback;

    /**
     * Initializes the route file.
     *
     * @param string $route The route array.
     */
    public function __construct(array $route)
    {
        // Set method
        $this->method = $route['method'] ?? '';
        if (empty($this->method) || !is_string($this->method)) {
            $msg = 'Invalid or missing method.';
            throw new InvalidRouteException($msg);
        }

        // Set URL
        $this->url = $route['url'] ?? '';
        if (empty($this->url) || !is_string($this->url)) {
            $msg = 'Invalid or missing URL.';
            throw new InvalidRouteException($msg);
        }

        // Set parameter's schema
        $this->parameters = $route['parameters'] ?? [];
        if (!is_array($this->parameters)) {
            $msg = 'Invalid or missing parameter schema.';
            throw new InvalidRouteException($msg);
        }

        // Set request schema
        $this->requestBody = $route['request'] ?? [];
        if (!is_array($this->requestBody)) {
            $msg = 'Invalid or missing request schema.';
            throw new InvalidRouteException($msg);
        }

        // Set response schema
        $this->responseBody = $route['response'] ?? [];
        if (!is_array($this->responseBody)) {
            $msg = 'Invalid or missing response schema.';
            throw new InvalidRouteException($msg);
        }

        // Set callback
        $this->callback = $route['callback'] ?? '';
        if (!is_callable($this->callback)) {
            $msg = 'Invalid or missing callback.';
            throw new InvalidRouteException($msg);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function requestBody(): array
    {
        return $this->requestBody;
    }

    /**
     * {@inheritDoc}
     */
    public function responseBody(): array
    {
        return $this->responseBody;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $req, Response $res): void
    {
        call_user_func($this->callback, $req, $res);
    }
}
