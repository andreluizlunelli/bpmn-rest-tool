<?php
/**
 * User: André Lunelli <andre@microton.com.br>
 * Date: 11/09/2017
 */

namespace andreluizlunelli\BpmnRestTool\Middleware;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Slim\Http\Response;

class AuthMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        /**
         * TODO Por a chamada da validação do api aqui dentro desse metodo
         *
         * @see https://pt.stackoverflow.com/questions/28235/colocar-authorization-basic-na-api
         */

        if ( ! isset( $_SERVER['HTTP_X_AUTH']) || empty( $_SERVER['HTTP_X_AUTH'])) {
            return $response->withJson('Necessário informar o token de acesso. Enviar post em /token')->withStatus(401);
        }

        $token = (new Parser())->parse( $_SERVER['HTTP_X_AUTH'] );

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer('http://localhost:8080');
        $data->setAudience('http://localhost:8080');

        if ( ! $token->validate($data))
            return $response
                ->withJson('Token expirado')
                ->withStatus(500);

        $response = $next($request, $response);
        return $response;

    }
}