<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 24/02/2019
 * Time: 14:45
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use andreluizlunelli\BpmnRestTool\Model\Authorization\AuthorizationUser;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use Zend\Session\SessionManager;

class LoginController extends ControllerBase
{

    public function login(Request $request, Response $response, $args)
    {
        $args['flashMessage'] = $this->message();
        if ($request->isGet())
            return $this->view()->render($response, 'login.twig', $args);

        /** @var SessionManager $session */
        $session = $this->container->get('SessionManager');

        $email = $request->getParam('email', '');
        $password = $request->getParam('password', '');

        $auth = new AuthorizationUser(
            $this->container->get('em')
            , $session
        );

        try {
            $auth->start($email, $password);
            return $response->withRedirect(
                $this->route()->pathFor('index'));
        } catch (\Throwable $t) {
            $this->errorMessage($t->getMessage());
            return $response->withRedirect(
                $this->route()->pathFor('login'));
        }
    }

    public function logout(Request $request, Response $response, $args)
    {
        /** @var SessionManager $session */
        $session = $this->container->get('SessionManager');
        $session->destroy();

        return $response->withRedirect(
            $this->route()->pathFor('login'));
    }

}

