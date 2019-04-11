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

    public function toArrayXml(): array
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

    public function getInnerElement(): array
    {
        return current($this->toArrayXml());
    }

    /**
     * @deprecated usar o toArrayXml TODO REMOVER ESSA FUNÇÃO E REMOVER A ASSINATURA DA INTERFACE CreateArrayForXml
     *
     * @param string $incoming
     * @param string $outgoing
     * @return array
     */
    public function createArrayForXml(string $incoming = '', string $outgoing = ''): array
    { //todo ?????????? pq passa o incoming e outgoing
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

    public static function createFromArray(array $seqArray): self
    {
        $s = new self($seqArray['_attributes']['sourceRef'], $seqArray['_attributes']['targetRef']);
        $s->id = $seqArray['_attributes']['id'];
        return $s;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
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

    public function newCreateArrayForXml(?Sequence $incoming, ?Sequence $outgoing): array
    {
        throw new \Exception('não usado');
    }

}
