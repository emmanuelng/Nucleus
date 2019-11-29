<?php

declare(strict_types=1);

namespace Nucleus\Router\Policies;

use Nucleus\Router\Policy;

/**
 * Represents the default router policy.
 */
class DefaultPolicy implements Policy
{
    /**
     * {@inheritDoc}
     */
    public function allowedOrigins(): ?array
    {
        return Policy::DEFAULT_ORIGINS;
    }

    /**
     * {@inheritDoc}
     */
    public function allowedMethods(): ?array
    {
        return Policy::DEFAULT_METHODS;
    }

    /**
     * {@inheritDoc}
     */
    public function allowedHeaders(): ?array
    {
        return Policy::DEFAULT_HEADERS;
    }
}
