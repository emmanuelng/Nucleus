<?php

use Nucleus\Router\Request;
use Nucleus\Router\Response;

/**
 * Test callback.
 *
 * @param Request $req The request.
 * @param Response $res The response.
 * @return void
 */
function functionCallback(Request $req, Response $res): void
{
    $res->setCode(200);
}
