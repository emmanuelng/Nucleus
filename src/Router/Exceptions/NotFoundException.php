<?php

declare(strict_types=1);

namespace Nucleus\Router\Exceptions;

class NotFoundException extends HttpException
{
    private $method;
    private $url;

    public function __construct(string $method, string $url)
    {
        parent::__construct("Not found.", 404);

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
