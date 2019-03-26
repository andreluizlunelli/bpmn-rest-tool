<?php

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;
use Spatie\ArrayToXml\ArrayToXml;

class BpmnBuilder
{
    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

    /**
     * @var array
     */
    private $rootXml;

    /**
     * BpmnBuilder constructor.
     * @param TypeElementAbstract $rootEl
     */
    public function __construct(TypeElementAbstract $rootEl)
    {
        $this->rootEl = $rootEl;
    }

    public function buildXml(): string
    {
        $this->rootXml = $sequences = $rxml = [];
        $previousEl = $nextEl = null;
        $actualEl = $this->rootEl;

        $this->rootXml = $this->createNode($previousEl, $actualEl, $this->getNextEl($actualEl), $sequences, $rxml);

        $this->createSequencesNode($this->rootXml, $sequences);

        $processNode = [
            'process' => [
                '_attributes' => [
                    'id' => 'Process_1'
                    , 'isExecutable' => false
                ]
            ]
        ];

        $processNode['process'] = array_merge($processNode['process'], $this->rootXml);

        $processNode['bpmndi:BPMNDiagram'] = $this->createXmlLayoutShape($this->rootXml);

        try {
            $a = ArrayToXml::convert($processNode, [
                'rootElementName' => 'definitions'
                , '_attributes' => [
                    'xmlns' => "http://www.omg.org/spec/BPMN/20100524/MODEL"
                    , 'xmlns:bpmndi' => "http://www.omg.org/spec/BPMN/20100524/DI"
                    , 'xmlns:omgdi' => "http://www.omg.org/spec/DD/20100524/DI"
                    , 'xmlns:omgdc' => "http://www.omg.org/spec/DD/20100524/DC"
                    , 'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance"
                ],
            ]);
        } catch (\DOMException $e) {
            throw $e;
        }

        return $this->normalizeXMLString($a);
    }

    /**
     * @param TypeElementAbstract $previousEl
     * @param TypeElementAbstract $actualEl
     * @param TypeElementAbstract $nextEl
     * @param array $sequences
     * @param array $rxml
     * @return array
     * @throws \Exception
     */
    private function createNode($previousEl = null, $actualEl, $nextEl = null, array &$sequences, array &$rxml): array
    {
        $incoming = $outgoing = null;
        switch (get_class($actualEl)) {
            case StartEvent::class:
                $sourceRef = $actualEl ? $actualEl->getId() : '';
                $targetRef = $nextEl ? $nextEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);

                $r = $actualEl->createArrayForXml(
                    $incoming ? $incoming->getId() : ''
                    , $outgoing ? $outgoing->getId() : ''
                );

                $tmpPrev = $actualEl;
                $tmpActual = $nextEl;
                $tmpNext = $this->getNextEl($nextEl);

                $mergeArr = array_merge($rxml, $r);

                return $this->createNode($tmpPrev, $tmpActual, $tmpNext, $sequences, $mergeArr);
                break;

            case EndEvent::class:
                $sourceRef = $previousEl ? $previousEl->getId() : '';
                $targetRef = $actualEl ? $actualEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);

                $r = $actualEl->createArrayForXml(
                    $incoming ? $incoming->getId() : ''
                    , $outgoing ? $outgoing->getId() : ''
                );

                $rxml = array_merge($rxml, $r);

                return $rxml;
            case TaskActivity::class:
                $sourceRef = $previousEl ? $previousEl->getId() : '';
                $targetRef = $actualEl ? $actualEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);

                $sourceRef = $actualEl ? $actualEl->getId() : '';
                $targetRef = $nextEl ? $nextEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);

                $r = $actualEl->createArrayForXml(
                    $incoming ? $incoming->getId() : ''
                    , $outgoing ? $outgoing->getId() : ''
                );

                $tmpPrev = $actualEl;
                $tmpActual = $nextEl;
                $tmpNext = $this->getNextEl($nextEl);

                $k = key($r);
                if ($previousEl instanceof SubProcess) {
                    $tasks[$k][] = $r[$k];

                    $tmpR = ! empty($tmpActual)
                        ? $this->createNode($tmpPrev, $tmpActual, $tmpNext, $sequences, $tasks)
                        : $r;

                    $rxml['subProcess'][key($tmpR)] = current($tmpR);

                    return $rxml;

                } else {
                    $rxml[$k][] = $r[$k];
                    return ! empty($tmpActual)
                        ? $this->createNode($tmpPrev, $tmpActual, $tmpNext, $sequences, $rxml)
                        : $rxml;
                }
                break;

            case SubProcess::class:
                $sourceRef = $previousEl ? $previousEl->getId() : '';
                $targetRef = $actualEl ? $actualEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);

                $rEnd = null;
                if ($actualEl->getOutgoing() instanceof EndEvent) { // tenho que criar um elemento de finalização e uma sequence pra ele
                    $a = [];
                    $rEnd = $this->createNode($actualEl, $actualEl->getOutgoing(), null, $sequences, $a);
                } else {
                    $sourceRef = $actualEl ? $actualEl->getId() : '';
                    $targetRef = $nextEl ? $nextEl->getId() : '';
                    if ( ! empty($sourceRef) && ! empty($targetRef))
                        $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);

                }
                // lembrar que subprocessos são identados no xml e não apenas sequenciais
                $r = $actualEl->createArrayForXml(
                    $incoming ? $incoming->getId() : ''
                    , $outgoing ? $outgoing->getId() : ''
                );

                $mergeArr = array_merge($rxml, $r);

                $tmpPrev = $actualEl;
                $tmpActual = $nextEl;
                $tmpNext = $this->getNextEl($nextEl);

                $tmpR = ! empty($tmpActual)
                    ? $this->createNode($tmpPrev, $tmpActual, $tmpNext, $sequences, $mergeArr['subProcess'])
                    : $r;

                $rxml['subProcess'][] = $tmpR;
                if ( ! empty($rEnd))
                    $rxml['endEvent'] = current($rEnd);

                if (! empty($actualEl->getOutgoing()) && $actualEl->getOutgoing() instanceof SubProcess) {

                    $tmpPrev = $actualEl->getOutgoing();
                    $tmpActual = $this->getNextEl($actualEl->getOutgoing());
                    $tmpNext = $this->getNextEl($tmpActual);

                    $sourceRef = $tmpPrev ? $tmpPrev->getId() : '';
                    $targetRef = $tmpActual ? $tmpActual->getId() : '';
                    if ( ! empty($sourceRef) && ! empty($targetRef))
                        $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);

                    $rEnd = null;
                    if ($actualEl->getOutgoing() instanceof EndEvent) { // tenho que criar um elemento de finalização e uma sequence pra ele
                        $a = [];
                        $rEnd = $this->createNode($actualEl, $actualEl->getOutgoing(), null, $sequences, $a);
                    } else {
                        $sourceRef = $tmpActual ? $tmpActual->getId() : '';
                        $targetRef = $tmpNext ? $tmpNext->getId() : '';
                        if ( ! empty($sourceRef) && ! empty($targetRef))
                            $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);

                    }

                    $r = $tmpPrev->createArrayForXml(
                        $incoming ? $incoming->getId() : ''
                        , $outgoing ? $outgoing->getId() : ''
                    );

                    $b = [];
                    $tmpR = ! empty($tmpPrev)
                        ? $this->createNode($tmpPrev, $tmpActual, $tmpNext, $sequences, $b)
                        : $r;

                    $teste = array_merge($r['subProcess'], $tmpR);

                    $rxml['subProcess'][] = $teste;
                }

                return $rxml;
                break;
            default:
                throw new \Exception('Não existe a classe para o nodo');
        }
        return $r;
    }

    private function createSequencesNode(array &$xml, array $sequences): void
    {
        array_walk($sequences, function ($item, $k) use (&$xml) {
            /** @var Sequence $item */
            return $xml['sequenceFlow'][] = $item->createArrayForXml()['sequenceFlow'];
        });
    }

    private function addSequence($sourceRef, $targetRef, &$sequences): Sequence
    {
        // verifica se já exite antes de dar um array push

        /** @var Sequence $s */
        foreach ($sequences as $s)
            if (($s->getSourceRef() == $sourceRef)
                && ($s->getTargetRef() == $targetRef))
                return $s;

        $seq = new Sequence($sourceRef, $targetRef);
        array_push($sequences, $seq);
        return $seq;
    }

    /**
     * Sequência de processamento
     * startEvent
     * sequenceFlow
     * subProcess/task
     * endEvent
     *
     * @param array $xml
     * @return array
     */
    private function createXmlLayoutShape(array $xml): array
    {

    }

    private function _createXmlLayoutShape(array $xml): array
    {
        $xmlLayout = [];

        $sequenceFlow = $xml['sequenceFlow'] ?? null;
        $startEvent = $xml['startEvent'] ?? null;
        $endEvent = $xml['endEvent'] ?? null;
        $task = $xml['task'] ?? null;

        $margemTopoPagina = 184;
        $margemEsquerdaInicial = 286;

        $medidasSequence = [ 'w' => 62, 'h' => 20];
        $medidasTask = [ 'w' => 100, 'h' => 80];
        $medidasStartEvent = [ 'w' => 36, 'h' => 36];
        $medidasEndEvent = $medidasStartEvent;

        $sumMedidasSequence = ['w' => $margemEsquerdaInicial + $medidasStartEvent['w'], 'h' => $medidasSequence['h']];
        $sumMedidasTask = ['w' => ($margemEsquerdaInicial + $medidasStartEvent['w'] + $medidasSequence['w']), 'h' => $medidasTask['h']];
        $sumMedidasStartEvent = ['w' => $margemEsquerdaInicial, 'h' => $medidasSequence['h']];
        $sumMedidasEndEvent = ['w' => 0, 'h' => 0];

        $h = $margemEsquerdaInicial; $s = 66;

        if (! empty($sequenceFlow)) {
            array_walk($xml['sequenceFlow'], function ($item, $k) use (&$xmlLayout, &$margemEsquerdaInicial
                , &$margemTopoPagina, &$medidasSequence, &$medidasTask, &$medidasStartEvent, &$medidasEndEvent
                , &$sumMedidasSequence, &$sumMedidasTask, &$sumMedidasStartEvent, &$sumMedidasEndEvent) {

                $el['_attributes']['id'] = $item['_attributes']['id'] . '_gui';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $paddingInternoTopo = 18;

                $y = $margemTopoPagina + $paddingInternoTopo;
                $x = $sumMedidasSequence['w'];

                $waypoint1 = ['_attributes' => [
                        'x' => $x
                        , 'y' => $y]];

                $x += $medidasSequence['w'];

                $waypoint2 = ['_attributes' => [
                        'x' => $x
                        , 'y' => $y]];

                $sumMedidasSequence['w'] = $x + $medidasTask['w'];

                $el['waypoint'][] = $waypoint1;
                $el['waypoint'][] = $waypoint2;
                $xmlLayout['BPMNEdge'][] = $el;
            });
        }

        if (! empty($startEvent)) {
            array_walk($xml['startEvent'], function ($item, $k) use (&$xmlLayout, &$margemEsquerdaInicial
                , &$margemTopoPagina, &$medidasSequence, &$medidasTask, &$medidasStartEvent, &$medidasEndEvent
                , &$sumMedidasSequence, &$sumMedidasTask, &$sumMedidasStartEvent, &$sumMedidasEndEvent) {

                $el['_attributes']['id'] = $item['_attributes']['id'] . '_di';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $x = $sumMedidasStartEvent['w'];

                $el['Bounds'] = [ '_attributes' => [
                        'x' => $x
                        , 'y' => $margemTopoPagina
                        , 'width' => $medidasStartEvent['w']
                        , 'height' => $medidasStartEvent['h']]];
                // não precisa somar pq só vai ter um elemento inicial

                $xmlLayout['BPMNShape'][] = $el;
            });
        }

        if (! empty($task)) {
            array_walk($xml['task'], function ($item, $k) use (&$xmlLayout, &$margemEsquerdaInicial
                , &$margemTopoPagina, &$medidasSequence, &$medidasTask, &$medidasStartEvent, &$medidasEndEvent
                , &$sumMedidasSequence, &$sumMedidasTask, &$sumMedidasStartEvent, &$sumMedidasEndEvent) {

                $el['_attributes']['id'] = $item['_attributes']['id'] . '_di';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $x = $sumMedidasTask['w'];

                $el['Bounds'] = [ '_attributes' => [
                    'x' => $x
                    , 'y' => $margemTopoPagina
                    , 'width' => $medidasTask['w']
                    , 'height' => $medidasTask['h']]];

                $sumMedidasTask['w'] = $x + $medidasTask['w'] + $medidasSequence['w'];

                $xmlLayout['BPMNShape'][] = $el;
            });
        }

        if (! empty($endEvent)) {
            array_walk($xml['endEvent'], function ($item, $k) use (&$xmlLayout, &$margemEsquerdaInicial
                , &$margemTopoPagina, &$medidasSequence, &$medidasTask, &$medidasStartEvent, &$medidasEndEvent
                , &$sumMedidasSequence, &$sumMedidasTask, &$sumMedidasStartEvent, &$sumMedidasEndEvent) {

                $el['_attributes']['id'] = $item['_attributes']['id'] . '_di';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $el['Bounds'] = [ '_attributes' => [
                    'x' => $sumMedidasTask['w']
                    , 'y' => $margemTopoPagina
                    , 'width' => $medidasEndEvent['w']
                    , 'height' => $medidasEndEvent['h']]];

                $xmlLayout['BPMNShape'][] = $el;
            });
        }

        $a = [];
        $a['bpmndi:BPMNDiagram']['_attributes']['id'] = 'BpmnDiagram_1';
        $a['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane']['_attributes']['id'] = 'BpmnPlane_1';
        $a['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane']['_attributes']['bpmnElement'] = 'Process_1';
        $a['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane']['BPMNEdge'] = $xmlLayout['BPMNEdge'];
        $a['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane']['BPMNShape'] = $xmlLayout['BPMNShape'];
        return $a['bpmndi:BPMNDiagram'];
    }

    private function normalizeXMLString(string $a): string
    {
        $a = str_replace('<BPMNEdge', '<bpmndi:BPMNEdge', $a);
        $a = str_replace('</BPMNEdge>', '</bpmndi:BPMNEdge>', $a);

        $a = str_replace('<waypoint', '<omgdi:waypoint', $a);

        $a = str_replace('<BPMNShape', '<bpmndi:BPMNShape', $a);
        $a = str_replace('</BPMNShape>', '</bpmndi:BPMNShape>', $a);

        $a = str_replace('<Bounds', '<omgdc:Bounds', $a);

        return $a;
    }

    private function getNextEl(?TypeElementAbstract $el): ?TypeElementAbstract
    {
        if (empty($el))
            return null;

        return $el instanceof SubProcess
            ? $el->getSubprocess()
            : $el->getOutgoing();
    }

}
