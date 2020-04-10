<?php

declare(strict_types=1);

namespace Nucleus\Router\Responses;

use Nucleus\Json\JsonObject;
use Nucleus\Router\Response;
use Nucleus\Router\Routes\ResolvedRoute;
use Nucleus\Schema\Exceptions\InvalidValueException;

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
    public function setBody(JsonObject $data): void
    {
        try {
            $values = $data->values();
            $this->res->setBody(new JsonObject($values));
        } catch (InvalidValueException $e) {
            $msg = "The response body must be empty.";
            throw new InvalidValueException($msg);
        }
    }
}
