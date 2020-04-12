<?php

declare(strict_types=1);

namespace Nucleus\Router\Requests;

use Nucleus\Json\JsonObject;
use Nucleus\Router\Exceptions\BadRequestException;
use Nucleus\Router\Request;
use Nucleus\Router\Route;
use Nucleus\Router\Routes\ResolvedRoute;
use Nucleus\Schema\Exceptions\InvalidValueException;
use Nucleus\Schema\Schema;

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
     * The request parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The request body.
     *
     * @var JsonObject
     */
    protected $body;

    /**
     * Initializes the request.
     *
     * @param Request $req The original request
     * @param ResolvedRoute $route The route
     */
    public function __construct(Request $req, ResolvedRoute $route)
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
    public final function body(): JsonObject
    {
        return $this->body;
    }

    /**
     * Filters the parameters of the original request and stores them.
     *
     * @param Request $req The original request.
     * @param ResolvedRoute $route The route
     * @return void
     */
    private function setParams(Request $req, ResolvedRoute $route): void
    {
        try {
            $schema = $route->parameters();
            if ($schema === null) {
                $this->parameters = [];
            } else {
                $urlParams = $route->urlParameters();
                $reqParams = $req->parameters();
                $allParams = array_merge($urlParams, $reqParams);

                $this->parameters = $schema->filter($allParams);
            }
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
            $schema = $schema !== null ? $schema : new Schema([]);
            $values = $req->body()->values();

            $this->body = new JsonObject($values, $schema);

        } catch (InvalidValueException $e) {
            throw new BadRequestException($e->getMessage());
        }
    }
}
