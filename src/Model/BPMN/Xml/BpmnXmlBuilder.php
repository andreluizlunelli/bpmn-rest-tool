<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 13:38
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;

class BpmnXmlBuilder
{

    public function build(TypeElementAbstract $root): BpmnXml
    {
        $_p = new ParamEl(null, $root, $root->getOutgoing());

        $bpmnXml = $this->behavior($_p);

        return $bpmnXml;
    }

    private function innerCreateNode(TypeElementAbstract $el): BpmnXml
    {
        $creator = $this->getCreator($el);
        return $creator->create($el);
    }

    private function getCreator(TypeElementAbstract $el): ElCreator
    {
        switch (get_class($el)) {
            case StartEvent::class: return new StartEventCreator(); break;
            case TaskActivity::class: return new TaskActivityCreator(); break;
            default: throw new \Exception('nao achou o criador');
        }
    }

    private function behavior(ParamEl $_p): BpmnXml
    {
        $bpmnXml = $this->innerCreateNode($_p->getActualEl());

        $this->posBehavior($_p, $bpmnXml);
    }

    private function posBehavior(ParamEl $_p, BpmnXml $bpmnXml): BpmnXml
    {
        switch (get_class($_p->getActualEl())) {
            case StartEvent::class:
                $el = $_p->getActualEl();
                // FAZ O MERGE ARRAY DO XML
                $xml = $bpmnXml->getXml();
                $nextEl = $el->getOutgoing();
                $bpmnXmlNext = $this->behavior(new ParamEl($el, $nextEl, null));
                $xml[$nextEl->getNameKey()] = $bpmnXmlNext->getXml();
                $bpmnXml->setXml($xml);
                // FAZ O MERGE ARRAY DO SEQUENCE
                $sequences = $bpmnXml->getSequences();
                $sequencesNext = $bpmnXmlNext->getSequences();
                $sequences = array_merge($sequences, $sequencesNext);
                $bpmnXml->setSequences($sequences);
                break;
            default: throw new \Exception('nao achou o criador');
        }
    }

}
