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

    /**
     * @var CalcShape
     */
    private $calcShapeFirst;

    private $returnXml = [];

    /**
     * EdgeElement constructor.
     * @param array $xml
     * @param $sequences
     * @param CalcShape $calcShape
     * @throws \Exception
     */
    public function __construct(array $xml, $sequences, CalcShape $calcShape)
    {
        $this->xml = $xml;

        if (empty($sequences))
            throw new \Exception('xml não possue sequences');

        $this->sequences = $sequences;
        $this->calcShapeFirst = $calcShape;
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

        $this->createNode($process, $this->calcShapeFirst);
        return $this->returnXml;
    }

    private function createNode(RawSubProcess $process, CalcShape &$calcShape): void
    {
        // CRIA O START BPMNShape
        $shapeStartXml = (new ShapeElement())->xmlFromRawStart($process->start, $calcShape);
        $this->pushShape($this->returnXml, $shapeStartXml);

        // CRIA O SEQUENCE_FLOW BPMNEdge
        $sequence = $this->createSequenceFlow($process->start->getOutgoing(), $calcShape);
        $this->pushSequence($this->returnXml, $sequence);

        /* CRIA O SUBPROCESS OU TASK BPMNShape */
        if (! empty($process->listSubProcess))
            $this->createNodeListSubProcess($process->listSubProcess, $calcShape);
        else
            $this->createNodeListTask($process->listTask, $calcShape);

        // CRIA O END BPMNShape o sequenceFlow anterior já é criado pelo createNodeListTask()
        $shapeEndXml = (new ShapeElement())->xmlFromRawEnd($process->end, $calcShape);
        $this->pushShape($this->returnXml, $shapeEndXml);
    }

    private function getRawStart(array $xml): RawStart
    {
        $attr = current($xml[StartEvent::getNameKey()]);

        return new RawStart($attr['_attributes']['id'], $attr['_attributes']['name'], $attr['outgoing']);
    }

    private function getRawEnd(array $xml): RawEnd
    {
        if ( ! array_key_exists(EndEvent::getNameKey(), $xml))
            return new RawEnd('','','');
        $attr = current($xml[EndEvent::getNameKey()]);

        return new RawEnd($attr['_attributes']['id'], $attr['_attributes']['name'], $attr['incoming']);
    }

    private function createSequenceFlow(string $outgoing, CalcShape $calcShape): array
    {
        $search = array_map(function(Sequence $item) {
            return $item->getId();
        }, $this->sequences);
        $k = array_search($outgoing, $search);
        $seq = $this->sequences[$k];
        return (new EdgeElement($seq))->xml($calcShape);
    }

    private function createNodeListSubProcess(?array $listSubProcess, CalcShape &$calcShape): void
    {
        $listCalcShape = [new CalcShape($calcShape->getX(), $calcShape->getY())];
        CalcShape::$sumWSubprocess = 0;
        array_walk($listSubProcess, function($subProcess) use (&$listCalcShape) {
            /** @var CalcShape $prevCalc */
            $prevCalc = end($listCalcShape);

            $_sub = $subProcess[SubProcess::getNameKey()] ?? null;
            $_task = $subProcess[TaskActivity::getNameKey()] ?? null;

            $process = new RawSubProcess(
                $subProcess['_attributes']['id']
                , $subProcess['_attributes']['name']
                , $this->getRawStart($subProcess)
                , $this->getRawEnd($subProcess)
                , $_sub, $_task
            );

            $p = $prevCalc->getxySubprocess();

            $espacoTitulo = 50;
            $newCalcShape = new CalcShape($prevCalc->getX(), $prevCalc->getY() + $espacoTitulo);

            array_push($listCalcShape, $newCalcShape);

            $this->createNode($process, $newCalcShape);

            $shape = (new ShapeElement())->innerXml(
                $subProcess['_attributes']['id'] . '_di', $subProcess['_attributes']['id']
                , $p->getX() - CalcShape::$sumWSubprocess/2
                , $p->getY()
                , CalcShape::$elSubprocess['w'] + CalcShape::$sumWSubprocess
                , $newCalcShape->getY()-$prevCalc->getY()
                , true);
            $this->pushShape($this->returnXml, $shape);

            CalcShape::$sumWSubprocess += CalcShape::$incWSubprocess;

            $sequence = $this->createSequenceFlow($subProcess['incoming'], $newCalcShape);
            $this->pushSequence($this->returnXml, $sequence);

            $newCalcShape->setY($newCalcShape->getY() - CalcShape::$elSequence['h']);
            $sequence = $this->createSequenceFlow($subProcess['outgoing'], $newCalcShape);
            $this->pushSequence($this->returnXml, $sequence);
        });
        /** @var CalcShape $end */
        $end = end($listCalcShape);
        $calcShape->setX($end->getX());
        $calcShape->setY($end->getY());
    }

    private function createNodeListTask(?array $listTask, CalcShape &$calcShape): void
    {
        array_walk($listTask, function($task) use (&$calcShape) {

            $p = $calcShape->getxyTask();

            $shape = (new ShapeElement($task, TaskActivity::getNameKey()))->innerXml($task['_attributes']['id'] . '_di', $task['_attributes']['id'], $p->getX(), $p->getY(), CalcShape::$elTask['w'], CalcShape::$elTask['h']);

            $this->pushShape($this->returnXml, $shape);

            $sequence = $this->createSequenceFlow($task['outgoing'], $calcShape);
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
