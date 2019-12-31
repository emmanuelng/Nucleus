<?php

declare(strict_types=1);

namespace Tests\Router\Classes;

use Nucleus\Router\Request;
use Nucleus\Router\Response;
use Nucleus\Router\Route;

/**
 * This class is used to test the route class.
 */
class TestRoute implements Route
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
     * The callback to execute when the route is executed.
     *
     * @var callable
     */
    private $onExecute;

    /**
     * The parameter schema.
     *
     * @var array
     */
    private $parameters;

    /**
     * The request schema.
     *
     * @var array
     */
    private $request;

    /**
     * The response schema.
     *
     * @var array
     */
    private $response;

    /**
     * Indicates whether the route was executed.
     *
     * @var bool
     */
    private $wasExecuted;

    /**
     * The request object received in the `execute()` method.
     *
     * @var Request
     */
    private $receivedRequest;

    /**
     * Initializes the test route.
     *
     * @param string $method The request method.
     * @param string $url The URL.
     * @param callable $onExecute The callback to execute when the route
     * is executed.
     */
    public function __construct(
        string $method,
        string $url,
        callable $onExecute = null
    ) {
        $this->method           = $method;
        $this->url              = $url;
        $this->onExecute        = $onExecute;
        $this->parameters       = [];
        $this->request          = [];
        $this->response         = [];
        $this->wasExecuted      = false;
        $this->receivedRequest  = null;
    }

    /**
     * Sets the parameter schema of the route.
     *
     * @param array $schema The schema.
     * @return void
     */
    public function setParameterSchema(array $schema): void
    {
        $this->parameters = $schema;
    }

    /**
     * Sets the request schema.
     *
     * @param array $schema The schema.
     * @return void
     */
    public function setRequestSchema(array $schema): void
    {
        $this->request = $schema;
    }

    /**
     * Sets the response schema.
     *
     * @param array $schema The schema
     * @return void
     */
    public function setResponseSchema(array $schema): void
    {
        $this->response = $schema;
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
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function responseBody(): array
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $req, Response $res): void
    {
        $this->wasExecuted     = true;
        $this->receivedRequest = $req;

        // Execute the callback
        if ($this->onExecute !== null) {
            call_user_func($this->onExecute, $req, $res);
        }
    }

    /**
     * Returns whether the route was executed or not.
     *
     * @return boolean True if the route was executed, false otherwise.
     */
    public function wasExecuted(): bool
    {
        return $this->wasExecuted;
    }

    /**
     * Returns the request object received in the `execute()` method. Returns
     * null if the route was not executed.
     *
     * @return Request|null
     */
    public function receivedRequest(): ?Request
    {
        return $this->receivedRequest;
    }
}
