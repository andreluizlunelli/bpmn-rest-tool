<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 11:45
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use Psr\Container\ContainerInterface;

class ControllerBase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @return \Slim\Views\Twig
     */
    protected function view()
    {
        return $this->container->get('view');
    }
}