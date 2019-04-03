<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 14:58
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;

class StartEventCreator extends ElCreator
{

    /**
     * @param TypeElementAbstract $el
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
            throw new \Exception('StartEvent precisa de sourceRef e targetRef');

        $outgoingSequence = $this->makeSequence($sourceRef, $targetRef);

        $r = $el->newCreateArrayForXml(null, $outgoingSequence);

        return new BpmnXml($r, $this->sequences);
    }

    private function makeSequence(string $sourceRef, string $targetRef): Sequence
    {
        $outgoing = $this->addSequence($sourceRef, $targetRef);
        $rxml['sequenceFlow'][] = current($outgoing->createArrayForXml());
        return $outgoing;
    }

}
