<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 15:55
 */

namespace andreluizlunelli\BpmnRestTool\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

interface MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, callable $next): ResponseInterface;
}