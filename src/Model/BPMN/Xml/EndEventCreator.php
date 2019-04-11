<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 18:27
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

class EndEventCreator extends ElCreator
{
    /**
     * @param ParamEl $paramEl
     * @return BpmnXml
     * @throws \Exception
     */
    public function create(ParamEl $paramEl): BpmnXml
    {
        $sourceRef = $paramEl->getPrevEl()->getId();
        $targetRef = $paramEl->getActualEl()->getId();
        if (empty($sourceRef) || empty($targetRef))
            throw new \Exception('EndEvent precisa de sourceRef e targetRef incoming');

        $incomingSequence = $this->addSequence($sourceRef, $targetRef);

        $el = $paramEl->getActualEl();
        $r = $el->newCreateArrayForXml($incomingSequence, null);
        $this->addSequenceFlowToArray($r, $incomingSequence);

        return new BpmnXml($r, $this->sequences);
    }

}
