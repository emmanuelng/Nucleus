<?php

declare(strict_types=1);

namespace Nucleus\Router\Responses;

use Nucleus\Router\Response;
use Nucleus\Router\Route;
use Nucleus\Router\Routes\ResolvedRoute;
use Nucleus\Types\Exceptions\InvalidValueException;

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
     * @var ResolvedRoute
     */
    private $route;

    /**
     * Initializes the response.
     *
     * @param Response $res The original response
     * @param ResolvedRoute $route The route
     */
    public function __construct(Response $res, ResolvedRoute $route)
    {
        $this->res   = $res;
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
        $schema = $this->route->responseBody();

        if ($schema === null) {
            $msg = "The response body must be empty.";
            throw new InvalidValueException($msg);
        }

        $this->res->setBody($schema->filter($data));
    }
}
