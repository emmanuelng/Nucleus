<?php

declare(strict_types=1);

namespace Tests\Router\Classes;

use Nucleus\Router\Response;

/**
 * This class is used to simulate a response produced by the server.
 */
class TestResponse implements Response
{
    /**
     * The response headers.
     *
     * @var array
     */
    private $headers;

    /**
     * The HTTP response code.
     *
     * @var int
     */
    private $code;

    /**
     * The response body.
     *
     * @var array
     */
    private $body;

    /**
     * {@inheritDoc}
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritDoc}
     */
    public function setBody(array $data): void
    {
        $this->body = $data;
    }

    /**
     * Returns the response headers.
     *
     * @return array The headers.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Returns the HTTP response code.
     *
     * @return integer
     */
    public function code(): int
    {
        return $this->code;
    }

    /**
     * Returns the response body.
     *
     * @return array
     */
    public function body(): array
    {
        return $this->body;
    }
}
