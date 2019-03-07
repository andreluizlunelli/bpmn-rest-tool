<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 11:45
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Router;

class ControllerBase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    protected function em(): EntityManager
    {
        return $this->container->get('em');
    }

    protected function view(): \Slim\Views\Twig
    {
        return $this->container->get('view');
    }

    protected function route(): Router
    {
        return $this->container->get('router');
    }

    protected function message(): Messages
    {
        return $this->container->get('message');
    }

    protected function errorMessage(string $msg): void
    {
        $this->message()->addMessage('error', $msg);
    }

}