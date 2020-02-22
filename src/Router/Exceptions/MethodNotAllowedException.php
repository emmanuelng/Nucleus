<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

class MethodNotAllowedException extends HttpException
{
    private $method;
    private $url;

    public function __construct(string $method, string $url)
    {
        parent::__construct("Method not allowed.", 405);

        $this->method = $method;
        $this->url    = $url;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function url(): string
    {
        return $this->url;
    }
}
