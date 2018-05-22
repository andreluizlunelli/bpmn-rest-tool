<?php
/**
 * User: André Lunelli <andre@microton.com.br>
 * Date: 03/11/2017
 */

namespace andreluizlunelli\BpmnRestTool\Middleware;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Slim\Http\Request;
use Slim\Http\Response;

class AutorizacaoMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        $stringToken = $_SESSION['admin']['token'] ?? '';

        if (strlen($stringToken) < 1)
            return  $response->withRedirect('/login');

        $token = (new Parser())->parse( $_SESSION['admin']['token'] );

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer('http://localhost:8080');
        $data->setAudience('http://localhost:8080');

        if ( ! $token->validate($data)) {
            return $response->withRedirect('/login');
        }

        $response = $next($request, $response);

        return $response;
    }
}