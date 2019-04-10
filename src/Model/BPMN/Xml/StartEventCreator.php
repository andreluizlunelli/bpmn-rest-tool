<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 14:58
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

class StartEventCreator extends ElCreator
{
    /**
     * @param ParamEl $paramEl
     * @return BpmnXml
     * @throws \Exception
     */
    public function create(ParamEl $paramEl): BpmnXml
    {
        $el = $paramEl->getActualEl();
        $outgoingEl = $el->getOutgoing();
        $sourceRef = $el ? $el->getId() : '';
        $targetRef = $outgoingEl ? $outgoingEl->getId() : '';
        if (empty($sourceRef) || empty($targetRef))
            throw new \Exception('StartEvent precisa de sourceRef e targetRef outgoing');

        $outgoingSequence = $this->addSequence($sourceRef, $targetRef);

        $r = $el->newCreateArrayForXml(null, $outgoingSequence);
        $this->addSequenceFlowToArray($r, $outgoingSequence);

        return new BpmnXml($r, self::$sequences);
    }

}
