<?php
/**
 * Criado por: andre.lunelli
 * Date: 04/04/2019 - 17:15
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

class SubProcessCreator extends ElCreator
{
    /**
     * @param ParamEl $paramEl
     * @return BpmnXml
     * @throws \Exception
     */
    public function create(ParamEl $paramEl): BpmnXml
    {
        $el = $paramEl->getActualEl();
        $sourceRef = $paramEl->getPrevEl() ? $paramEl->getPrevEl()->getId() : '';
        $targetRef = $el ? $el->getId() : '';
        if (empty($sourceRef) || empty($targetRef))
            throw new \Exception('SubProcessCreator precisa de sourceRef e targetRef de incoming');

        $incomingSequence = $this->addSequence($sourceRef, $targetRef);

        $sourceRef = $el ? $el->getId() : '';
        $targetRef = $paramEl->getNextEl() ? $paramEl->getNextEl()->getId() : '';
        if (empty($sourceRef) || empty($targetRef))
            throw new \Exception('SubProcessCreator precisa de sourceRef e targetRef de outgoing');

        $outgoingSequence = $this->addSequence($sourceRef, $targetRef);

        $r = $el->newCreateArrayForXml($incomingSequence, $outgoingSequence);
        $this->addSequenceFlowToArray($r, $incomingSequence);
        $this->addSequenceFlowToArray($r, $outgoingSequence);

        return new BpmnXml($r, self::$sequences);
    }

}
