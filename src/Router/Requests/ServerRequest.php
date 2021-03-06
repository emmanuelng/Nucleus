<?php

declare(strict_types=1);

namespace Nucleus\Router\Requests;

use Nucleus\Router\JsonObject;
use Nucleus\Router\Request;

/**
 * Represents a server request.
 */
class ServerRequest implements Request
{
    /**
     * {@inheritDoc}
     */
    public final function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * {@inheritDoc}
     */
    public final function url(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * {@inheritDoc}
     */
    public final function headers(): array
    {
        return getallheaders();
    }

    /**
     * {@inheritDoc}
     */
    public final function parameters(): JsonObject
    {
        return new JsonObject($_GET);
    }

    /**
     * {@inheritDoc}
     */
    public final function body(): JsonObject
    {
        return new JsonObject(json_decode(file_get_contents('php://input')));
    }
}
