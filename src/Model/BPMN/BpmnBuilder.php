<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 19/06/2018
 * Time: 00:13
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use Spatie\ArrayToXml\ArrayToXml;

class BpmnBuilder
{
    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

    /**
     * BpmnBuilder constructor.
     * @param TypeElementAbstract $rootEl
     */
    public function __construct(TypeElementAbstract $rootEl)
    {
        $this->rootEl = $rootEl;
    }

    public function buildXml(): string
    {
        $xml = $sequence = [];
        $previousEl = $nextEl = null;
        $actualEl = $this->rootEl;

        do {
            $arrayForXml = $actualEl->createArrayForXml();

            $k = key($arrayForXml);

            $xml[$k][] = $arrayForXml[$k];

            /** @var TypeElementAbstract $nextEl */
            $nextEl = current($actualEl->getOutgoing()); // aqui pode vir uma lista, que seria uma arvore binária

            $previousEl = $actualEl;

            $actualEl = $nextEl;

        } while ( ! empty($nextEl));

        $a = ArrayToXml::convert($xml, [
            'rootElementName' => 'definitions'
            , '_attributes' => [
                'xmlns' => 'http://www.omg.org/spec/BPMN/20100524/MODEL'
            ],
        ]);
        return $a;
    }

}