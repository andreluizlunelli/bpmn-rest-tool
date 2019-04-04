<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 13:38
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;

class BpmnXmlBuilder
{
    private $outlineLevelBuffer;

    public function build(TypeElementAbstract $root): BpmnXml
    {
        $paramEl = new ParamEl(null, $root, $root->getOutgoing());

        $bpmnXml = $this->behavior($paramEl);

        return $bpmnXml;
    }

    private function innerCreateNode(ParamEl $paramEl): BpmnXml
    {
        $creator = $this->getCreator($paramEl->getActualEl());
        return $creator->create($paramEl);
    }

    private function getCreator(TypeElementAbstract $el): ElCreator
    {
        switch (get_class($el)) {
            case StartEvent::class: return new StartEventCreator(); break;
            case TaskActivity::class: return new TaskActivityCreator(); break;
            case EndEvent::class: return new EndEventCreator(); break;
            default: throw new \Exception('nao achou o criador');
        }
    }

    private function behavior(ParamEl $paramEl): BpmnXml
    {
        $bpmnXml = $this->innerCreateNode($paramEl);

        return $this->posBehavior($paramEl, $bpmnXml);
    }

    /**
     * Arranja a ordem do (anterior, atual, próximo) dentro do ParamEl
     * faz merge do proximo elemento com elemento atual
     *
     * TODO:     para identar corretamente os nodos criados
     * TODO:     usa o outlineLevel que está dentro do projectTask para definir qual elemento vai ser o "buffer"
     * TODO:     para fazer o "array_merge" dos nodos criados
     * TODO:
     * TODO:     QUANDO EU INSERIR UM SUBPROCESS É O MARCO DE UM NOVO OUTLINElEVEL
     *
     * @param ParamEl $paramEl
     * @param BpmnXml $bpmnXml
     * @return BpmnXml
     * @throws \Exception
     */
    private function posBehavior(ParamEl $paramEl, BpmnXml $bpmnXml): BpmnXml
    {
        $el = $paramEl->getActualEl();
        // FAZ O MERGE ARRAY DO XML
        $xml = $bpmnXml->getXml();
        $sequences = $bpmnXml->getSequences();

        if ($paramEl->getActualEl() instanceof EndEvent)
            return $bpmnXml;

        $nextEl = $el->getOutgoing();
       /*todo: se o actualEl for um EndEvent, não existe nextEl !! */
        $bpmnXmlNext = $this->behavior(new ParamEl($el, $nextEl, $nextEl ? $nextEl->getOutgoing() : null));
        $xml[$nextEl->getNameKey()] = $bpmnXmlNext->getXml();
        $bpmnXml->setXml($xml);
        // FAZ O MERGE ARRAY DO SEQUENCE

        $sequencesNext = $bpmnXmlNext->getSequences();
        $sequences = array_merge($sequences, $sequencesNext);
        $bpmnXml->setSequences($sequences);
        return $bpmnXml;
    }

}
