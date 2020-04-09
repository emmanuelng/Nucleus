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
        $route1 = new TestRoute('GET', '/test');
        $this->router->addRoute($route1);

        $route2 = new TestRoute('POST', '/test');
        $this->router->addRoute($route2);

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

        $route1 = new TestRoute('GET', $url1);
        $this->router->addRoute($route1);

        $route2 = new TestRoute('GET', $url2);
        $this->router->addRoute($route2);
    }

    /**
     * Tests whether the router generates not found errors.
     *
     * @return void
     */
    public function testHandlesUndefinedUrls(): void
    {
        $route = new TestRoute('GET', '/test');
        $this->router->addRoute($route);

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
        $route = new TestRoute('GET', '/test');
        $this->router->addRoute($route);

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
        $route = new TestRoute('GET', '/test');
        $this->router->addRoute($route);

        $route->setParameterSchema(['a' => ['type' => 'number']]);
        $route->setRequestSchema(['c' => ['type' => 'boolean']]);

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
        $route = new TestRoute('GET', '/test');
        $route->setParameterSchema(['a' => ['type' => 'number', 'required' => true]]);
        $this->router->addRoute($route);

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
        $route = new TestRoute('GET', '/test');
        $route->setRequestSchema(['a' => ['type' => 'number', 'required' => true]]);
        $this->router->addRoute($route);

        // Invalid value
        $res = $this->sendRequest('GET', '/test', [], [], ['a' => 'abc']);
        $this->assertEquals(400, $res->code());

        // Missing value
        $res = $this->sendRequest('GET', '/test');
        $this->assertEquals(400, $res->code());
    }

    /**
     * Tests whether the router is able to extract URL parameter and to pass
     * them to the execute method.
     *
     * @return void
     */
    public function testSupportsUrlParameters(): void
    {
        $route = new TestRoute('GET', '/test/:p1/abc/:p2');
        $route->setParameterSchema([
            'p1' => ['type' => 'number', 'required' => true],
            'p2' => ['type' => 'string', 'required' => true]
        ]);

        $this->router->addRoute($route);

        $this->sendRequest('GET', '/test/1/abc/test');
        $this->assertTrue($route->wasExecuted());

        $receivedParams = $route->receivedRequest()->parameters();
        $this->assertSame(['p1' => 1, 'p2' => 'test'], $receivedParams);
    }

    /**
     * Tests that the router handles missing route parameter correctly.
     *
     * @return void
     */
    public function testHandlesMissingUrlParameter(): void
    {
        // Mandatory parameter
        $route1 = new TestRoute('GET', '/test1/:p');
        $route1->setParameterSchema([
            'p' => ['type' => 'number', 'required' => true]
        ]);

        $this->router->addRoute($route1);

        $res1 = $this->sendRequest('GET', '/test1');
        $this->assertFalse($route1->wasExecuted());
        $this->assertEquals(404, $res1->code());

        // Optional parameter
        $route2 = new TestRoute('GET', '/test2/:p');
        $route2->setParameterSchema([
            'p' => ['type' => 'number']
        ]);

        $this->router->addRoute($route2);

        $res2 = $this->sendRequest('GET', '/test2');
        $this->assertTrue($route2->wasExecuted());
        $this->assertEquals(200, $res2->code());
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
