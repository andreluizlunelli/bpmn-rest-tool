<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 14:58
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;

abstract class ElCreator
{
    protected $xml = [];
    protected static $sequences = [];
    abstract public function create(ParamEl $paramEl): BpmnXml;

    protected function addSequence($sourceRef, $targetRef): Sequence
    {
        // verifica se já exite antes de dar um array push
        /** @var Sequence $s */
        foreach (self::$sequences as $s)
            if (($s->getSourceRef() == $sourceRef)
                && ($s->getTargetRef() == $targetRef))
                return $s;

        $seq = new Sequence($sourceRef, $targetRef);
        array_push(self::$sequences, $seq);
        return $seq;
    }

    public function getXml(): array
    {
        return $this->xml;
    }

    public function getSequences(): array
    {
        return self::$sequences;
    }

    protected function addSequenceFlowToArray(array &$r, Sequence $s): void
    {
        $r['sequenceFlow'][] = current($s->toArrayXml());
    }

}
