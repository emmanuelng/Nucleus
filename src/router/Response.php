<?php

declare(strict_types=1);

namespace Nucleus\Router;

/**
 * Represents a router's response.
 */
interface Response
{
    /**
     * Sets a response header.
     *
     * @param string $name The header's name.
     * @param string $value The header's value.
     * @return void
     */
    public function setHeader(string $name, string $value): void;

    /**
     * Sets the response HTTP code.
     *
     * @param integer $code The HTTP code.
     * @return void
     */
    public function setCode(int $code): void;

    /**
     * Sets the response body.
     *
     * @param array $data Data sent to the sender.
     * @return void
     */
    public function setBody(array $data): void;
}
