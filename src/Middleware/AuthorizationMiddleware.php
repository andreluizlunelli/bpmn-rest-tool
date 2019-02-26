<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 16:04
 */

namespace andreluizlunelli\BpmnRestTool\Middleware;

use andreluizlunelli\BpmnRestTool\System\App;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use Zend\Session\SessionManager;

class AuthorizationMiddleware implements MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, callable $next): ResponseInterface
    {
        $container = App::getApp()->getContainer();
        /** @var SessionManager $session */
        $session = $container->get('SessionManager');

        if (empty($session->getStorage()->getMetadata('user')))
            return $response->withRedirect(
                $container->get('router')->pathFor('login')
                , StatusCode::HTTP_MOVED_PERMANENTLY);

        return $next($request, $response);
    }

}