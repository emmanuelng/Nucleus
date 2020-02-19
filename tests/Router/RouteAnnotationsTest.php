<?php

declare(strict_types=1);

namespace Tests\Router;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Request;
use Nucleus\Router\Response;
use Nucleus\Router\Route;
use Nucleus\Router\Routes\RouteAnnotations;
use PHPUnit\Framework\TestCase;
use Tests\Types\Classes\TestSchema;

/**
 * @Route\method    GET
 * @Route\url       /test
 * @Route\params    Tests\Types\Classes\TestSchema
 * @Route\request   Tests\Types\Classes\TestSchema
 * @Route\response  Tests\Types\Classes\TestSchema
 */
class AnnotatedRoute1 implements Route
{
    use RouteAnnotations;

    public function execute(Request $req, Response $res): void
    {
        // Empty...
    }
}

/**
 * @Route\method  GET
 * @Route\url     /test
 */
class AnnotatedRoute2 implements Route
{
    use RouteAnnotations;

    public function execute(Request $req, Response $res): void
    {
        // Empty...
    }
}

/**
 * @Route\method    GET
 * @Route\url       /test
 * @Route\params    InvalidSchema
 * @Route\request   InvalidSchema
 * @Route\response  InvalidSchema
 */
class AnnotatedRoute3 implements Route
{
    use RouteAnnotations;

    public function execute(Request $req, Response $res): void
    {
        // Empty...
    }
}

/**
 * Tests the route annotations.
 */
class RouteAnnotationsTest extends TestCase
{
    /**
     * Tests that routes can be correctly configured using annotations.
     *
     * @return void
     */
    public function testAnnotationsWork(): void
    {
        $testRoute = new AnnotatedRoute1();
        $this->assertEquals('GET', $testRoute->method());
        $this->assertEquals('/test', $testRoute->url());
        $this->assertInstanceOf(TestSchema::class, $testRoute->parameters());
        $this->assertInstanceOf(TestSchema::class, $testRoute->requestBody());
        $this->assertInstanceOf(TestSchema::class, $testRoute->responseBody());
    }

    /**
     * Tests that null is returned if the parameter, request or response schema
     * aren't specified.
     *
     * @return void
     */
    public function testReturnsNullIfMissingSchema(): void
    {
        $testRoute = new AnnotatedRoute2();
        $this->assertNull($testRoute->parameters());
        $this->assertNull($testRoute->requestBody());
        $this->assertNull($testRoute->responseBody());
    }

    /**
     * Tests that an exception is thrown if the parameter schema is an
     * invalid class.
     *
     * @return void
     */
    public function testThrowsExceptionIfParameterSchemaIsInvalid(): void
    {
        $testRoute = new AnnotatedRoute3();
        $this->expectException(InvalidRouteException::class);
        $testRoute->parameters();
    }

    /**
     * Tests that an exception is thrown if the request schema is an
     * invalid class.
     *
     * @return void
     */
    public function testThrowsExceptionIfRequestSchemaIsInvalid(): void
    {
        $testRoute = new AnnotatedRoute3();
        $this->expectException(InvalidRouteException::class);
        $testRoute->requestBody();
    }

    /**
     * Tests that an exception is thrown if the response schema is an
     * invalid class.
     *
     * @return void
     */
    public function testThrowsExceptionIfResponseSchemaIsInvalid(): void
    {
        $testRoute = new AnnotatedRoute3();
        $this->expectException(InvalidRouteException::class);
        $testRoute->responseBody();
    }
}
