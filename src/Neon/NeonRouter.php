<?php

declare(strict_types=1);

namespace Nucleus\Neon;

use Nucleus\Router\Policy;
use Nucleus\Router\Router;

/**
 * A router generated from NEON files.
 */
class NeonRouter extends Router
{
    /**
     * Initializes the NEON router.
     *
     * @param string $path The path of the router.
     * @param string $baseUrl The router's base URL.
     * @param Policy|null $policy The router's policy.
     */
    public function __construct(
        string $path,
        string $baseUrl = '',
        ?Policy $policy = null
    ) {
        // Initialize the router
        parent::__construct($baseUrl, $policy);

        // Compile the routes
        $routes = NeonCompiler::compile($path);
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }
}
