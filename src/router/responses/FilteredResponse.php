<?php

declare(strict_types=1);

namespace Nucleus\Router\Responses;

use Nucleus\Filter\Filter;
use Nucleus\Router\Response;
use Nucleus\Router\Route;

/**
 * Represents a filtered response. Wraps a response and makes sure that it is
 * compatible with a route's specifications.
 */
class FilteredResponse implements Response
{
    /**
     * The original response.
     *
     * @var Response
     */
    private $res;

    /**
     * The route associated to the response.
     *
     * @var Route
     */
    private $route;

    /**
     * Initializes the response.
     *
     * @param Response $res The original response
     * @param Route $route The route associated to the response
     */
    public function __construct(Response $res, Route $route)
    {
        $this->res = $res;
        $this->route = $route;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeader(string $name, string $value): void
    {
        $this->res->setHeader($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function setCode(int $code): void
    {
        $this->res->setCode($code);
    }

    /**
     * {@inheritDoc}
     */
    public function setBody(array $data): void
    {
        $schema   = $this->route->responseBody();
        $filtered = Filter::value($schema, $data);
        $this->res->setBody($filtered);
    }
}
