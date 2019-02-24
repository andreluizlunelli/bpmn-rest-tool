<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 24/02/2019
 * Time: 14:45
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class LoginController extends ControllerBase
{
    public function login(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, 'login.twig', $args);
    }
}