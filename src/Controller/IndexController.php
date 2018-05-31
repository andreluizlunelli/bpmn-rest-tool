<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 11:45
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends ControllerBase
{
    public function index(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, 'controller/index/index.twig', $args);
    }

}