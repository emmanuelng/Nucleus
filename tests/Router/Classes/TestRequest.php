<?php

declare(strict_types=1);

namespace Tests\Router\Classes;

use Nucleus\Json\JsonObject;
use Nucleus\Router\Request;

/**
 * This class is used to simulte a request received by a router.
 */
class TestRequest implements Request
{
    /**
     * The request method.
     *
     * @var string
     */
    private $method;

    /**
     * The request URL.
     *
     * @var string
     */
    private $url;

    /**
     * The request headers.
     *
     * @var array
     */
    private $headers;

    /**
     * The request parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * The request body.
     *
     * @var JsonObject
     */
    private $body;

    public function __construct(
        string $method,
        string $url,
        array $headers    = [],
        array $parameters = [],
        array $body       = []
    ) {
        $this->method     = $method;
        $this->url        = $url;
        $this->headers    = $headers;
        $this->parameters = $parameters;
        $this->body       = new JsonObject($body);
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
    public function headers(): array
    {
        return $this->headers;
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
    public function body(): JsonObject
    {
        return $this->body;
    }
}
