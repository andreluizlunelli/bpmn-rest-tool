<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 19/09/2017
 * Time: 23:17
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use Lcobucci\JWT\Builder;
use Psr\Container\ContainerInterface;

class TokenController
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function post(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        $body = json_decode((string) $request->getBody(), true);

        $login = $body['login'] ?? '';
        $pass = $body['pass'] ?? '';

        if ( empty($login) || empty($pass))
            return $response
                ->withJson('Necessario informar as credenciais')
                ->withStatus(401);

        if ($login != "admin" || $pass != "admin")
            return $response
                ->withJson('Usuario não autenticado.')
                ->withStatus(401);

        $token = $this->createToken();

        return $response
            ->withJson(['token' => $token->__toString()])
            ->withStatus(200);
    }

    /**
     * @return \Lcobucci\JWT\Token
     */
    public function createToken()
    {
        return (new Builder())->setIssuer($this->container->get('settings')['url']) // Configures the issuer (iss claim)
        ->setAudience($this->container->get('settings')['url']) // Configures the audience (aud claim)
        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
        ->setNotBefore(time()) // Configures the time that the token can be used (nbf claim)
        ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
        ->getToken();
    }
}