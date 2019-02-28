<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 24/02/2019
 * Time: 14:29
 */

namespace andreluizlunelli\BpmnRestTool\Model\Twig;

use Psr\Container\ContainerInterface;
use Twig_Extension;
use Twig_Function;

class ViewFunctions extends Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ViewFunctions constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_Function('base_assets', [$this, 'baseAssets'])
        ];
    }

    public function baseAssets(): string
    {
        return $this->container->get('settings')['base_assets'] ?? '';
    }

}