<?php

declare(strict_types=1);

namespace Nucleus\Router\Requests;

use Nucleus\Router\Exceptions\BadRequestException;
use Nucleus\Router\Request;
use Nucleus\Router\Route;
use Nucleus\Types\Exceptions\InvalidValueException;

/**
 * Represents a filtered request, i.e. a safe request for a route. This class
 * wraps another request and ensures that the data is always filtered before
 * it is accessed.
 */
class FilteredRequest implements Request
{
    /**
     * The request method.
     *
     * @var string
     */
    protected $method;

    /**
     * The request URL.
     *
     * @var string
     */
    protected $url;

    /**
     * The request headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * The (filtered) request parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The (filtered) request body.
     *
     * @var array
     */
    protected $body;

    /**
     * Initializes the request.
     *
     * @param Request $req The original request
     * @param Route $route The route for which the request is made
     */
    public function __construct(Request $req, Route $route)
    {
        // Set method, url and headers
        $this->method  = $req->method();
        $this->url     = $req->url();
        $this->headers = $req->headers();

        // Filter parameters
        $this->setParams($req, $route);

        // Filter body
        $this->setBody($req, $route);
    }

    /**
     * {@inheritDoc}
     */
    public final function method(): string
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public final function url(): string
    {
        return $this->url;
    }

    /**
     * {@inheritDoc}
     */
    public final function headers(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public final function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public final function body(): array
    {
        return $this->body;
    }

    /**
     * Filters the parameters of the original request and stores them.
     *
     * @param Request $req The original request.
     * @param Route $route The route
     * @return void
     */
    private function setParams(Request $req, Route $route): void
    {
        try {
            $schema = $route->parameters();
            $this->parameters = $schema->filter($req->parameters());
        } catch (InvalidValueException $e) {
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Filters the body of the original request and stores it.
     *
     * @param Request $req The original request
     * @param Route $route The route
     * @return void
     */
    private function setBody(Request $req, Route $route): void
    {
        try {
            $schema = $route->requestBody();
            $this->body = $schema->filter($req->body());
        } catch (InvalidValueException $e) {
            throw new BadRequestException($e->getMessage());
        }
    }
}
