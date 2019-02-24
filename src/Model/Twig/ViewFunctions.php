<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 24/02/2019
 * Time: 14:29
 */

namespace andreluizlunelli\BpmnRestTool\Model\Twig;

use Twig_Extension;
use Twig_Function;

class ViewFunctions extends Twig_Extension
{
    /**
     * @var array
     */
    private $settings;

    /**
     * ViewFunctions constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_Function('base_assets', [$this, 'baseAssets'])
        ];
    }

    public function baseAssets(): string
    {
        return $this->settings['base_assets'] ?? '';
    }

}