<?php

declare(strict_types=1);

namespace Nucleus\Router;

use Exception;
use Nucleus\Router\Exceptions\HttpException;
use Nucleus\Router\Exceptions\InvalidRouteException;
use Nucleus\Router\Policies\DefaultPolicy;
use Nucleus\Router\Requests\FilteredRequest;
use Nucleus\Router\Requests\ServerRequest;
use Nucleus\Router\Resolvers\RegexResolver;
use Nucleus\Router\Responses\FilteredResponse;
use Nucleus\Router\Responses\ServerResponse;

/**
 * This class represents a router. A router manages routes and is responsible
 * for executing them based on a given input.
 */
class Router
{
    /**
     * Base URL from which the route URLs are defined. In general corresponds
     * to the host's URL.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * The router's policy.
     *
     * @var Policy
     */
    private $policy;

    /**
     * The URL resolver.
     *
     * @var Resolver
     */
    private $resolver;

    /**
     * Initializes the router.
     *
     * @param string $baseUrl The base URL. In most cases corresponds to the
     * host's URL.
     * @param Resolver|null $resolver The URL resolver to use. If null,
     * initializes the router with the default resolver.
     * @param Policy|null $policy The router's policy. If null, initializes
     * the router with the default policy.
     */
    public function __construct(
        string $baseUrl = '',
        ?Resolver $resolver = null,
        ?Policy $policy = null
    ) {
        $this->baseUrl  = $baseUrl;
        $this->resolver = $resolver;
        $this->policy   = $policy;

        // If no resolver was specified, use the default one.
        if ($resolver === null) {
            $this->resolver = new RegexResolver();
        }

        // If no policy was specified, use the default one.
        if ($policy === null) {
            $this->policy = new DefaultPolicy();
        }
    }

    /**
     * Adds a route to the router.
     *
     * @param Route $route The route.
     * @return void
     */
    public function addRoute(Route $route): void
    {
        // Get the route's method
        $method = strtoupper($route->method());
        if (empty($method)) {
            throw new InvalidRouteException('Missing method');
        }

        // Check if the method is allowed by the policy
        if (!in_array($method, $this->policy->allowedMethods())) {
            throw new InvalidRouteException('Method not allowed');
        }

        // Register the route
        $this->resolver->register($route);
    }

    /**
     * Handles a request and fills a response accordingly.
     *
     * @param Request $req The request
     * @param Response $res The response
     * @return void
     */
    public function handle(Request $req, Response $res): void
    {
        try {
            // Get the request method.
            $method = strtoupper($req->method());
            if ($method === 'OPTIONS') {
                $this->handlePreflightedRequests($res);
                return;
            }

            // Get the request URL.
            $url = $req->url();
            $baseUrlLen = strlen($this->baseUrl);

            if (substr($url, 0, $baseUrlLen) == $this->baseUrl) {
                $url = substr($url, $baseUrlLen);
            }

            // Resolve the URL.
            $route = $this->resolver->resolve($method, $url);

            // Execute the route.
            $filteredReq = new FilteredRequest($req, $route);
            $filteredRes = new FilteredResponse($res, $route);

            $route->execute($filteredReq, $filteredRes);
            $filteredRes->setCode(200);
        } catch (HttpException $e) {
            // HTTP error
            $res->setCode($e->getCode());
            $res->setBody(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            // System error
            $res->setCode(500);
            $res->setBody(['message' => $e->getMessage()]);
        }
    }

    /**
     * Handles preflighted requests, used by browsers to check whether a
     * request is safe. See https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
     * for more details.
     *
     * @param Response $res The response.
     * @return void
     */
    private function handlePreflightedRequests(Response $res): void
    {
        // Send allowed origins
        $originArr = $this->policy->allowedOrigins();
        $originStr = $originArr == null ? '*' : implode(', ', $originArr);
        $res->setHeader('Access-Control-Allow-Origin', $originStr);

        // Send the allowed methods
        $methodArr = $this->policy->allowedMethods();
        $methodStr = $methodArr == null ? '*' : implode(', ', $methodArr);
        $res->setHeader('Access-Control-Allow-Methods', $methodStr);

        // Send the allowed headers
        $headerArr = $this->policy->allowedHeaders();
        $headerStr = $headerArr == null ? '*' : implode(', ', $headerArr);
        $res->setHeader('Access-Control-Allow-Headers', $headerStr);
    }

    /**
     * Starts the router.
     *
     * @return void
     */
    public function start(): void
    {
        $this->handle(new ServerRequest(), new ServerResponse());
        die();
    }
}
