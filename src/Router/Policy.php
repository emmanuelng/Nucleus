<?php

declare(strict_types=1);

namespace Nucleus\Router;

/**
 * Defines the policy followed by a router. This includes various aspects, such
 * as the allowed request method, origins, etc.
 */
interface Policy
{
    /**
     * The default allowed origins.
     */
    const DEFAULT_ORIGINS = null;

    /**
     * The default allowed methods.
     */
    const DEFAULT_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * The default allowed headers.
     */
    const DEFAULT_HEADERS = null;

    /**
     * Returns the allowed request origins.
     *
     * @return array|null The list of allowed origins or null if all of them
     *                    are allowed.
     */
    public function allowedOrigins(): ?array;

    /**
     * Returns the allowed request methods.
     *
     * @return array|null The list of allowed methods or null if all of them
     *                    are allowed.
     */
    public function allowedMethods(): ?array;

    /**
     * Returns the allowed request headers.
     *
     * @return array|null The list of allowed headers or null if all of them
     *                    are allowed.
     */
    public function allowedHeaders(): ?array;
}
