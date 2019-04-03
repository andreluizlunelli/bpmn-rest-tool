<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 16:43
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

class TaskActivityCreator extends ElCreator
{
    public function create(ParamEl $paramEl): BpmnXml
    {
        $sourceRef = $paramEl->getPrevEl() ? $paramEl->getPrevEl()->getId() : '';
        $targetRef = $paramEl->getActualEl() ? $paramEl->getActualEl()->getId() : '';
        if ( ! empty($sourceRef) && ! empty($targetRef)) {
            $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);
            $rxml['sequenceFlow'][] = current($incoming->createArrayForXml());
        }

        $sourceRef = $actualEl ? $actualEl->getId() : '';
        $targetRef = $nextEl ? $nextEl->getId() : '';
        if ( ! empty($sourceRef) && ! empty($targetRef))
            $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);

        $r = $actualEl->createArrayForXml(
            $incoming ? $incoming->getId() : ''
            , $outgoing ? $outgoing->getId() : ''
        );
    }

}
