<?php

declare(strict_types=1);


namespace Tests\Router;

use Nucleus\Router\Routes\RouteFile;
use PHPUnit\Framework\TestCase;
use Tests\Router\Classes\TestRequest;
use Tests\Router\Classes\TestResponse;

require_once(__DIR__ . '/callbacks/callbacks.php');

class RouteFileTest extends TestCase
{
    const FILES_DIR = __DIR__ . '/files/';

    public function testParsesValidFiles(): void
    {
        $path1        = self::FILES_DIR . 'route_file_1.json';
        $route1       = new RouteFile($path1);
        $fileContents = json_decode(file_get_contents($path1), true);

        $this->assertEquals($fileContents['method'], $route1->method());
        $this->assertEquals($fileContents['url'], $route1->url());
        $this->assertEquals($fileContents['parameters'], $route1->parameters());
        $this->assertEquals($fileContents['request'], $route1->requestBody());
        $this->assertEquals($fileContents['response'], $route1->responseBody());

        $req1 = new TestRequest('GET', '/test1');
        $res1 = new TestResponse();

        $route1->execute($req1, $res1);
        $this->assertEquals(200, $res1->code());

        $path2  = self::FILES_DIR . 'route_file_2.json';
        $route2 = new RouteFile($path2);

        $req2 = new TestRequest('GET', '/test2');
        $res2 = new TestResponse();

        $route2->execute($req2, $res2);
        $this->assertEquals(200, $res2->code());
    }
}
