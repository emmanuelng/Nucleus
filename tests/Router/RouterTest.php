<?php

declare(strict_types=1);

namespace Tests\Router;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Router;
use PHPUnit\Framework\TestCase;
use Tests\Router\Classes\TestRequest;
use Tests\Router\Classes\TestResponse;
use Tests\Router\Classes\TestRoute;

class RouterTest extends TestCase
{
    /**
     * The router's base URL.
     */
    const BASE_URL = 'http://test.com/';

    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $this->router = new Router(self::BASE_URL);
    }

    /**
     * Tests whether the router executes the route corresponding to a request.
     *
     * @return void
     */
    public function testExecutesRoute(): void
    {
        $route1 = $this->addRoute('GET', '/test');
        $route2 = $this->addRoute('POST', '/test');

        $this->sendRequest('GET', 'test/');

        $this->assertTrue($route1->wasExecuted());
        $this->assertFalse($route2->wasExecuted());
    }

    /**
     * Duplicate URL data provider.
     *
     * @return array The data sets.
     */
    public function duplicateUrlProvider(): array
    {
        return [
            ['test', 'test'],
            ['/test', '/test'],
            ['test/', 'test/'],
            ['test', 'test/']
        ];
    }

    /**
     * The router should throw an exception if duplicate URLs are registered.
     *
     * @dataProvider duplicateUrlProvider
     */
    public function testRejectsDuplicateUrl(string $url1, string $url2): void
    {
        $this->expectException(InvalidRouteException::class);
        $this->addRoute('GET', $url1);
        $this->addRoute('GET', $url2);
    }

    /**
     * Tests whether the router generates not found errors.
     *
     * @return void
     */
    public function testHandlesUndefinedUrls(): void
    {
        $this->addRoute('GET', '/test');
        $res = $this->sendRequest('GET', 'undefined/');
        $this->assertEquals(404, $res->code());
    }

    /**
     * Test that 'method not allowed' errors are correctly generated.
     *
     * @return void
     */
    public function testHandlesMethodNotAllowed(): void
    {
        $this->addRoute('GET', '/test');
        $res = $this->sendRequest('POST', '/test');
        $this->assertEquals(405, $res->code());
    }

    /**
     * Tests that the request is filtered before calling the `execute()`
     * method.
     *
     * @return void
     */
    public function testFiltersValidRequests(): void
    {
        $route = $this->addRoute('GET', '/test');

        $route->setParameterSchema(['a' => ['type' => 'int']]);
        $route->setRequestSchema(['c' => ['type' => 'bool']]);

        $params = ['a' => '1234', 'b' => 'value'];
        $body   = ['c' => 'true', 'd' => 'value'];

        $res = $this->sendRequest('GET', '/test', [], $params, $body);
        $req = $route->receivedRequest();

        $this->assertEquals(200, $res->code());

        $this->assertSame(1234, $req->parameters()['a'] ?? false);
        $this->assertArrayNotHasKey('b', $req->parameters());

        $this->assertSame(true, $req->body()['c'] ?? false);
        $this->assertArrayNotHasKey('d', $req->body());
    }

    /**
     * Tests whether the router returns a `Bad request` error if invalid
     * parameters are sent.
     *
     * @return void
     */
    public function testHandlesInvalidParameters(): void
    {
        $route = $this->addRoute('GET', '/test');
        $route->setParameterSchema(['a' => ['type' => 'int']]);

        // Invalid value
        $res = $this->sendRequest('GET', '/test', [], ['a' => 'abc'], []);
        $this->assertEquals(400, $res->code());

        // Missing value
        $res = $this->sendRequest('GET', '/test', [], [], []);
        $this->assertEquals(400, $res->code());
    }

    /**
     * Tests whether the router returns a `Bad request` error if an
     * invalid request body is sent.
     *
     * @return void
     */
    public function testHandlesInvalidRequestBody(): void
    {
        $route = $this->addRoute('GET', '/test');
        $route->setRequestSchema(['a' => ['type' => 'int']]);

        // Invalid value
        $res = $this->sendRequest('GET', '/test', [], [], ['a' => 'abc']);
        $this->assertEquals(400, $res->code());

        // Missing value
        $res = $this->sendRequest('GET', '/test', [], [], []);
        $this->assertEquals(400, $res->code());
    }

    /**
     * Adds a route to the router.
     *
     * @param string $method The request method.
     * @param string $url The URL.
     * @param callable $onExecute The callable to execute when the
     * route is executed.
     * @return TestRoute The added route.
     */
    private function addRoute(
        string $method,
        string $url,
        callable $onExecute = null
    ): TestRoute {
        $route = new TestRoute($method, $url, $onExecute);
        $this->router->addRoute($route);
        return $route;
    }

    /**
     * Sends a request to the router.
     *
     * @param string $method The request method.
     * @param string $url The URL.
     * @param array $params The request parameters.
     * @param array $body THe request body.
     * @return TestResponse The response.
     */
    private function sendRequest(
        string $method,
        string $url,
        array $headers = [],
        array $params  = [],
        array $body    = []
    ): TestResponse {
        // Get the actual URL
        $url = self::BASE_URL . $url;

        // Initialize request and response
        $req = new TestRequest($method, $url, $headers, $params, $body);
        $res = new TestResponse();

        // Send request
        $this->router->handle($req, $res);
        return $res;
    }
}
