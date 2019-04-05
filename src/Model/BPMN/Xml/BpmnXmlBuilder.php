<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 13:38
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;

class BpmnXmlBuilder
{
    private $outlineLevelBuffer = [];

    /**
     * Retorna o XML <process> array montado
     * @param TypeElementAbstract $root
     * @return array
     */
    public function build(TypeElementAbstract $root): array
    {
        $paramEl = new ParamEl(null, $root, $root->getOutgoing());

        $this->behavior($paramEl);

        return $this->outlineLevelBuffer;
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
            case SubProcess::class: return new SubProcessCreator(); break;
            default: throw new \Exception('nao achou o criador');
        }
    }

    private function behavior(ParamEl $paramEl): BpmnXml
    {
        $bpmnXml = $this->innerCreateNode($paramEl);

        if ($paramEl->getActualEl() instanceof SubProcess)
            $this->posBehaviorSubProcess($paramEl, $bpmnXml);
        return $this->posBehavior($paramEl, $bpmnXml);
    }

    /**
     * Arranja a ordem do (anterior, atual, próximo) dentro do ParamEl
     * faz merge do proximo elemento com elemento atual
     *
     * TODO:     para identar corretamente os nodos criados
     * TODO:     usa o outlineLevel que está dentro do projectTask para definir qual elemento vai ser o "buffer"
     * TODO:     para fazer o "array_merge" dos nodos criados
     * TODO:     QUANDO EU INSERIR UM SUBPROCESS É O MARCO DE UM NOVO OUTLINElEVEL
     *
     * @param ParamEl $paramEl
     * @param BpmnXml $bpmnXml
     * @return BpmnXml
     * @throws \Exception
     */
    private function posBehavior(ParamEl $paramEl, BpmnXml $bpmnXml): BpmnXml
    {
        if ($paramEl->getActualEl() instanceof SubProcess
            && $paramEl->getNextEl() instanceof EndEvent) { // significa que ele já foi processado e já está no $this->outlineLevelBuf e o proximo é um endEvent
            $pEnd = new ParamEl(
                $paramEl->getActualEl()
                , $paramEl->getNextEl()
                , null
            );
            return $this->behavior($pEnd);
        }

        if ($paramEl->getActualEl() instanceof SubProcess
            && $paramEl->getNextEl() instanceof SubProcess) { // significa que ele já foi processado e já está no $this->outlineLevelBuf e o proximo é um SubProcess também
            $pSub = new ParamEl(
                $paramEl->getActualEl()
                , $paramEl->getNextEl()
                , $paramEl->getNextEl()->getOutgoing()
            );
            return $this->behavior($pSub);
        }

        $this->addToBuf($this->outlineLevelBuffer, $bpmnXml);

        if ($paramEl->getActualEl() instanceof EndEvent)
            return $bpmnXml;

        // se o next for subprocess tem que tratar antes do start event
        if ($paramEl->getActualEl() instanceof StartEvent
            || $paramEl->getActualEl() instanceof TaskActivity) {
            $pTask = new ParamEl(
                $paramEl->getActualEl()
                , $paramEl->getActualEl()->getOutgoing()
                , $paramEl->getActualEl()->getOutgoing()
                    ? $paramEl->getActualEl()->getOutgoing()->getOutgoing() : null);

            return $this->behavior($pTask);
        }

        throw new \Exception('Não fez nenhum processamento do posBehavior');
    }

    private function posBehaviorSubProcess(ParamEl $paramEl, BpmnXml $bpmnXml): BpmnXml
    {
        $copyBuf = $this->outlineLevelBuffer;
        $this->outlineLevelBuffer = [];


        $pSubProcess = new ParamEl(
            null
            , $paramEl->getActualEl()->getSubProcess()
            , $paramEl->getActualEl()->getSubProcess()->getOutgoing()
        );

        $ig = $this->behavior($pSubProcess); // ignoro o retorno pois o conteudo está em outlineLevelBuffer

        $buf = $this->addSubProcessToBuf($copyBuf, $this->outlineLevelBuffer, $bpmnXml);
        $this->outlineLevelBuffer = $buf;
        return $ig; // todo.. posBehavior* podem ser void
    }

    private function addToBuf(array &$buf, BpmnXml $bpmnXml): void
    {
        $key = $bpmnXml->getKey();
        foreach ($bpmnXml->getSequences() as $s) {
//            if ( ! $this->existSequenceFlow($s, $buf['sequenceFlow'] ?? []))
            $buf['sequenceFlow'][] = $s->getInnerElement();
        }

        $buf[$key][] = $bpmnXml->getInnerElement();
    }

    private function existSequenceFlow(Sequence $s, array $sequenceFlowRaw): bool
    {
        if (count($sequenceFlowRaw) < 1)
            return false;

        $inArray = array_map(function ($item) {
            return $item['_attributes']['sourceRef'] . $item['_attributes']['targetRef'];
        }, $sequenceFlowRaw);

        $needle = $s->getSourceRef() . $s->getTargetRef();

        return in_array($needle, $inArray);
    }

    private function addSubProcessToBuf(array $copyBuf, array $outlineLevelBuffer, BpmnXml $subProcess): array
    {
        foreach ($subProcess->getSequences() as $s) {
//            if ( ! $this->existSequenceFlow($s, $copyBuf['sequenceFlow'] ?? []))
            $copyBuf['sequenceFlow'][] = $s->getInnerElement();
        }

        $tmp = array_merge($subProcess->getInnerElement(), $outlineLevelBuffer);

        $copyBuf[SubProcess::getNameKey()][] = $tmp;

        return $copyBuf;
    }


    /* TODO
     * o incoming que sai do StartEvent ou do Subprocesso anterior, tem que ser o incoming dentro do projeto
     * o outgoing seque pelo mesmo caminho
     */


}
