<?php

use Nucleus\Router\Request;
use Nucleus\Router\Response;

/**
 * Function callback.
 *
 * @param Request $req The request.
 * @param Response $res The response.
 * @return void
 */
function functionCallback(Request $req, Response $res): void
{
    $res->setCode(200);
}

/**
 * Class containing a static callback method.
 */
class MyResource
{
    /**
     * Static callback method.
     *
     * @param Request $req
     * @param Response $res
     * @return void
     */
    public static function staticCallback(Request $req, Response $res): void
    {
        $res->setCode(200);
    }
}
