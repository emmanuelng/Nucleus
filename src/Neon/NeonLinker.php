<?php

declare(strict_types=1);

namespace Nucleus\Neon;

use Nucleus\Neon\Exceptions\LinkErrorException;

/**
 * Class used to link routes with their referenced schemas.
 */
class NeonLinker
{
    /**
     * The maximum reference depth.
     */
    const MAX_DEPTH = 128;

    /**
     * Links a list of routes with the defined schemas.
     *
     * Routes can reference schemas as types. The linkage process replaces these
     * references with the actual definitions of the types, so that only base
     * types remain.
     *
     * @param array $routes A list of arrays representing routes.
     * @param array $schemas The list of all defined schemas.
     * @return array A list of linked routes.
     */
    public static function link(array $routes, array $schemas): array
    {
        foreach ($routes as &$route) {
            self::linkRoute($route, $schemas);
        }

        return $routes;
    }

    /**
     * Links a particular route.
     *
     * @param array $route The route to link. Is modified by reference.
     * @param array $allSchemas The list of all defined schemas.
     * @return void
     */
    private static function linkRoute(
        array &$route,
        array &$allSchemas
    ): void {
        if (isset($route['parameters'])) {
            self::linkSchema($route['parameters'], $allSchemas);
        }

        if (isset($route['request'])) {
            self::linkSchema($route['request'], $allSchemas);
        }

        if (isset($route['response'])) {
            self::linkSchema($route['response'], $allSchemas);
        }
    }

    /**
     * Links a schema, i.e. replaces all its references with their actual
     * definitions.
     *
     * @param array $schema The schema to link.
     * @param array $allSchemas The list of all defined schemas.
     * @param integer $depth The current reference depth.
     * @return void
     */
    private static function linkSchema(
        array &$schema,
        array &$allSchemas,
        int $depth = 0
    ): void {
        if ($depth > self::MAX_DEPTH) {
            $msg = 'Link error: The maximum depth was reached. There might be '
                . 'reference cycles.';
            throw new LinkErrorException($msg);
        }

        foreach ($schema as &$field) {
            $type = $field['type'] ?? null;
            if ($type === null) {
                return;
            }

            if (is_string($type) && isset($allSchemas[$type])) {
                self::linkSchema($allSchemas[$type], $allSchemas, $depth + 1);
                $field['type'] = $allSchemas[$type];
                continue;
            }

            if (is_array($type)) {
                self::linkSchema($type, $allSchemas, $depth + 1);
                continue;
            }
        }
    }
}
