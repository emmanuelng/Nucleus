<?php

declare(strict_types=1);

namespace Nucleus\Router\Routes;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Types\Schema;
use ReflectionClass;

/**
 * Trait allowing to configure routes using annotations.
 */
trait RouteAnnotations
{
    /**
     * Returns the annotations of the class.
     *
     * @return array An associative array containg the annotations.
     */
    public static function classAnnotations(): array
    {
        static $annotations;

        if (!isset($annotations)) {
            $annotations = [];

            $rc       = new ReflectionClass(self::class);
            $docBlock = $rc->getDocComment();
            $docBlock = $docBlock === false ? '' : $docBlock;
            $lines    = [];

            preg_match_all('/\* *@.+ *.*\n/', $docBlock, $lines);

            foreach ($lines[0] ?? [] as $line) {
                $line  = trim(ltrim($line, '*'));
                $parts = explode(' ', $line, 2);
                $key   = ltrim($parts[0], '@');

                $annotations[$key] = $parts[1] ?? '';
            }
        }

        return $annotations;
    }

    /**
     * Returns the route's request method.
     * Set with the `@Route\method` annotation.
     *
     * @return string The method.
     */
    public function method(): string
    {
        $annotations = self::classAnnotations();
        return trim($annotations['Route\method'] ?? '');
    }

    /**
     * Returns the route's URL.
     * Set with the `@Route\url` annotation.
     *
     * @return string The URL.
     */
    public function url(): string
    {
        $annotations = self::classAnnotations();
        return trim($annotations['Route\url'] ?? '');
    }

    /**
     * Returns the route's parameter schema.
     * Set with the `@Route\params` annotation. The value must be the name of a
     * subclass of Nucleus\Types\Schema that has an empty contructor.
     *
     * @return Schema|null The schema or null if it isn't defined.
     */
    public function parameters(): ?Schema
    {
        $annotations = self::classAnnotations();
        $className   = trim($annotations['Route\params'] ?? '');

        if (empty($className)) {
            return null;
        }

        if (!class_exists($className)) {
            $msg = "Class $className not found.";
            throw new InvalidRouteException($msg);
        }

        if (!is_subclass_of($className, Schema::class)) {
            $msg = "$className must be a subclass of " . Schema::class;
            throw new InvalidRouteException($msg);
        }

        return new $className;
    }

    /**
     * Returns the route's parameter schema.
     * Set with the `@Route\request` annotation. The value must be the name of
     * a subclass of Nucleus\Types\Schema that has an empty contructor.
     *
     * @return Schema|null The schema or null if it isn't defined.
     */
    public function requestBody(): ?Schema
    {
        $annotations = self::classAnnotations();
        $className   = trim($annotations['Route\request'] ?? '');

        if (empty($className)) {
            return null;
        }

        if (!class_exists($className)) {
            $msg = "Class $className not found.";
            throw new InvalidRouteException($msg);
        }

        if (!is_subclass_of($className, Schema::class)) {
            $msg = "$className must be a subclass of " . Schema::class;
            throw new InvalidRouteException($msg);
        }

        return new $className;
    }

    /**
     * Returns the route's parameter schema.
     * Set with the `@Route\response` annotation. The value must be the name of
     * a subclass of Nucleus\Types\Schema that has an empty contructor.
     *
     * @return Schema|null The schema or null if it isn't defined.
     */
    public function responseBody(): ?Schema
    {
        $annotations = self::classAnnotations();
        $className   = trim($annotations['Route\response'] ?? '');

        if (empty($className)) {
            return null;
        }

        if (!class_exists($className)) {
            $msg = "Class $className not found.";
            throw new InvalidRouteException($msg);
        }

        if (!is_subclass_of($className, Schema::class)) {
            $msg = "$className must be a subclass of " . Schema::class;
            throw new InvalidRouteException($msg);
        }

        return new $className;
    }
}
