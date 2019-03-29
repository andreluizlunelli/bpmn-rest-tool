<?php
/**
 * Criado por: andre.lunelli
 * Date: 28/03/2019 - 08:42
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw\RawEnd;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw\RawStart;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw\RawSubProcess;

class ShapeBuilder
{
    /**
     * @var array
     */
    private $xml;

    /**
     * @var array
     */
    private $sequences;

    private $returnXml = [];

    /**
     * EdgeElement constructor.
     * @param array $xml
     * @param $sequences
     * @throws \Exception
     */
    public function __construct(array $xml, $sequences)
    {
        $this->xml = $xml;

        if (empty($sequences))
            throw new \Exception('xml não possue sequences');

        $this->sequences = $sequences;
    }

    public function xml(): array
    {
        /* definir precedencia de processamento
            primeiro:   startEvent
            segundo:    sequenceFlow
            terceiro:   subprocess
            quarto:     task
            quinto:     sequenceFlow
            quinto:     endEvent
        */

        $_sub = $this->xml[SubProcess::getNameKey()] ?? null;
        $_task = $this->xml[TaskActivity::getNameKey()] ?? null;

        $process = new RawSubProcess(
            '', ''
            , $this->getRawStart($this->xml)
            , $this->getRawEnd($this->xml)
            , $_sub, $_task
        );

        $this->createNode($process);
        return $this->returnXml;
    }

    private function createNode(RawSubProcess $process): void
    {
        // CRIA O START BPMNShape
        $shapeStartXml = (new ShapeElement())->xmlFromRawStart($process->start);
        $this->pushShape($this->returnXml, $shapeStartXml);

        // CRIA O SEQUENCE_FLOW BPMNEdge
        $sequence = $this->createSequenceFlow($process->start->getOutgoing());
        $this->pushSequence($this->returnXml, $sequence);

        /* CRIA O SUBPROCESS OU TASK BPMNShape */
        if (! empty($process->listSubProcess))
            $this->createNodeListSubProcess($process->listSubProcess);
        else
            $this->createNodeListTask($process->listTask);

        // CRIA O END BPMNShape
        $shapeEndXml = (new ShapeElement())->xmlFromRawEnd($process->end);
        $this->pushShape($this->returnXml, $shapeEndXml);
    }

    private function getRawStart(array $xml): RawStart
    {
        $attr = $xml[StartEvent::getNameKey()];

        return new RawStart($attr['_attributes']['id'], $attr['_attributes']['name'], $attr['outgoing']);
    }

    // todo pode não existir, mas não deveria!
    private function getRawEnd(array $xml): RawEnd
    {
        if ( ! array_key_exists(EndEvent::getNameKey(), $xml))
            return new RawEnd('','','');
        $attr = $xml[EndEvent::getNameKey()];

        return new RawEnd($attr['_attributes']['id'], $attr['_attributes']['name'], $attr['incoming']);
    }

    private function createSequenceFlow(string $outgoing): array
    {
        $search = array_map(function(Sequence $item) {
            return $item->getId();
        }, $this->sequences);
        $k = array_search($outgoing, $search);
        $seq = $this->sequences[$k];
        // todo: remover o $k encontrado da lista
        return (new EdgeElement($seq))->xml();
    }

    private function createNodeListSubProcess(?array $listSubProcess): void
    {
        array_walk($listSubProcess, function($subProcess) {
            $_sub = $subProcess[SubProcess::getNameKey()] ?? null;
            $_task = $subProcess[TaskActivity::getNameKey()] ?? null;

            $process = new RawSubProcess(
                $subProcess['_attributes']['id']
                , $subProcess['_attributes']['name']
                , $this->getRawStart($subProcess)
                , $this->getRawEnd($subProcess)
                , $_sub, $_task
            );

            $shape = (new ShapeElement())->innerXml($subProcess['_attributes']['id'] . '_di', $subProcess['_attributes']['id'], 50, 50, 100, 100);
            $this->pushShape($this->returnXml, $shape);

            $this->createNode($process);
        });
    }

    private function createNodeListTask(?array $listTask): void
    {
        array_walk($listTask, function($task) {
            $shape = (new ShapeElement($task, TaskActivity::getNameKey()))->innerXml($task['_attributes']['id'] . '_di', $task['_attributes']['id'], 50, 50, 100, 100);

            $this->pushShape($this->returnXml, $shape);

            $sequence = $this->createSequenceFlow($task['outgoing']);
            $this->pushSequence($this->returnXml, $sequence);
        });
    }

    private function pushSequence(array &$array, array $var): void
    {
        $array[EdgeElement::$keyShape][] = current($var);
    }

    private function pushShape(array &$array, array $var): void
    {
        $array[ShapeElement::$keyShape][] = current($var);
    }

}
