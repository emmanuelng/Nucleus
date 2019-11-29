<?php

declare(strict_types=1);

namespace Nucleus\Router\Routes;

use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Request;
use Nucleus\Router\Response;
use Nucleus\Router\Route;

/**
 * This class loads a route from a JSON file.
 *
 * - **method**: The request method.
 * - **url**: The URL
 * - **parameters**: The parameters schema
 * - **request**: The request schema
 * - **response**: The response schema
 * - **callback**: The callback. A callback must be a valid callable which
 *   takes a `Request` and a `Response` as parameters.
 *   Example:
 *   ```
 *   function myCallback(Request $res, Response $res): void {...}
 *   ```
 *
 * **Example file**: myRoute.json
 * ```
 * {
 *      'method': 'GET',
 *      'url': '/users',
 *      'parameters': {
 *          'user-id': {'type': 'string', 'default': ''}
 *      },
 *      'response': {
 *          'users': {
 *              'type': {'username': {'type': 'string'}},
 *              'isList': true
 *          }
 *      },
 *      'callback': 'User::get'
 * }
 * ```
 */
class RouteFile implements Route
{
    /**
     * The request method.
     *
     * @var string
     */
    private $method;

    /**
     * The URL.
     *
     * @var string
     */
    private $url;

    /**
     * The parameters schema.
     *
     * @var array
     */
    private $parameters;

    /**
     * The request schema.
     *
     * @var array
     */
    private $requestBody;

    /**
     * The response schema.
     *
     * @var array
     */
    private $responseBody;

    /**
     * The route's callback.
     *
     * @var callable
     */
    private $callback;

    /**
     * Initializes the route file.
     *
     * @param string $filePath The path
     */
    public function __construct(string $filePath)
    {
        // Read file
        $routeArr = json_decode(file_get_contents($filePath), true);
        if ($routeArr === null) {
            throw new InvalidRouteException('Invalid file content.');
        }

        // Set method
        $this->method = $routeArr['method'] ?? '';
        if (empty($this->method) || !is_string($this->method)) {
            throw new InvalidRouteException('Invalid method.');
        }

        // Set URL
        $this->url = $routeArr['url'] ?? '';
        if (empty($this->url) || !is_string($this->url)) {
            throw new InvalidRouteException('Invalid URL.');
        }

        // Set parameter's schema
        $this->parameters = $routeArr['parameters'] ?? [];
        if (!is_array($this->parameters)) {
            throw new InvalidRouteException('Invalid parameter schema.');
        }

        // Set request schema
        $this->requestBody = $routeArr['request'] ?? [];
        if (!is_array($this->requestBody)) {
            throw new InvalidRouteException('Invalid request schema.');
        }

        // Set response schema
        $this->responseBody = $routeArr['response'] ?? [];
        if (!is_array($this->responseBody)) {
            throw new InvalidRouteException('Invalid response schema.');
        }

        // Set callback
        $this->callback = $routeArr['callback'] ?? '';
        if (!is_callable($this->callback)) {
            throw new InvalidRouteException('Invalid callback.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function requestBody(): array
    {
        return $this->requestBody;
    }

    /**
     * {@inheritDoc}
     */
    public function responseBody(): array
    {
        return $this->responseBody;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Request $req, Response $res): void
    {
        call_user_func($this->callback, $req, $res);
    }
}
