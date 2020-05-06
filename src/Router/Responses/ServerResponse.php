<?php

declare(strict_types=1);

namespace Nucleus\Router\Responses;

use Nucleus\Router\JsonObject;
use Nucleus\Router\Response;

/**
 * Represents a server response.
 */
class ServerResponse implements Response
{
    /**
     * {@inheritDoc}
     */
    public function setHeader(string $name, string $value): void
    {
        header("$name: $value");
    }

    /**
     * {@inheritDoc}
     */
    public function setCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * {@inheritDoc}
     */
    public function setBody(JsonObject $data): void
    {
        $this->setHeader('Content-Type', 'application/json');
        echo $data;
    }
}
