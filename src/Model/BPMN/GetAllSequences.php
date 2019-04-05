<?php
/**
 * Criado por: andre.lunelli
 * Date: 05/04/2019 - 14:16
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;

class GetAllSequences
{
    private $processXml = [];

    /**
     * Pega o retorno do BpmnXmlBuilder->build e devolve todas as sequences
     * @see \andreluizlunelli\BpmnRestTool\Model\BPMN\Xml\BpmnXmlBuilder
     *
     * Utilizado na classe ShapeBuilder
     * @see \andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\ShapeBuilder
     *
     * GetAllSequences constructor.
     * @param array $processXml
     */
    public function __construct(array $processXml)
    {
        $this->processXml = $processXml;
    }

    /**
     * @return array lista de Sequences
     * @see Sequence
     */
    public function all(): array
    {
//        return array_map(function ($item) {
//            $sequence = new Sequence($item['_attributes']['sourceRef'], $item['_attributes']['targetRef']);
//            $sequence->setId($item['_attributes']['id']);
//            return $sequence;
//        }, $rawArraySequences);

        return $this->allRecursive($this->processXml);
    }

    private function allRecursive(array $xml): array
    {
        if ( ! array_key_exists('sequenceFlow', $xml))
            return [];

        $all = $this->transformSequence($xml['sequenceFlow']);

        if ( ! array_key_exists(SubProcess::getNameKey(), $xml))
            return $all;

        foreach ($xml[SubProcess::getNameKey()] as $itemSubProcess)
            $all = array_merge($all, $this->allRecursive($itemSubProcess));

        return $all;
    }

    private function transformSequence(array $aRawSequences): array
    {
        return array_map(function ($item) {
            $sequence = new Sequence($item['_attributes']['sourceRef'], $item['_attributes']['targetRef']);
            $sequence->setId($item['_attributes']['id']);
            return $sequence;
        }, $aRawSequences);
    }

}
