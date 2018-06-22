<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 21/06/2018
 * Time: 19:34
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\CreateArrayForXml;

class Sequence implements CreateArrayForXml
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $sourceRef;

    /**
     * @var string
     */
    private $targetRef;

    /**
     * Sequence constructor.
     * @param string $sourceRef
     * @param string $targetRef
     */
    public function __construct(string $sourceRef, string $targetRef)
    {
        $this->id = "sequenceFlow_".uniqid();
        $this->sourceRef = $sourceRef;
        $this->targetRef = $targetRef;
    }

    public function createArrayForXml(string $incoming = '', string $outgoing = ''): array
    {
        return [
            'sequenceFlow' => [
                '_attributes' => [
                    'id' => $this->id
                    , 'sourceRef' => $this->sourceRef
                    , 'targetRef' => $this->targetRef
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSourceRef(): string
    {
        return $this->sourceRef;
    }

    /**
     * @return string
     */
    public function getTargetRef(): string
    {
        return $this->targetRef;
    }

}