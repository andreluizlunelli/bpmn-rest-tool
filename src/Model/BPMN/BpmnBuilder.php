<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 19/06/2018
 * Time: 00:13
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use Spatie\ArrayToXml\ArrayToXml;

class BpmnBuilder
{
    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

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
        $xml = $sequences = [];
        $previousEl = $nextEl = $next2 = null;
        $actualEl = $this->rootEl;

        do {
            $arrayForXml = $this->createNode($previousEl, $actualEl, $next2, $sequences);

            $k = key($arrayForXml);
            $xml[$k][] = $arrayForXml[$k];

            /** @var TypeElementAbstract $nextEl */
            $nextEl = current($actualEl->getOutgoing()); // aqui poderá no futuro vir uma lista, que sera uma arvore binária

            $previousEl = $actualEl;
            $actualEl = $nextEl;

            $next2 = $nextEl ? current($nextEl->getOutgoing()) : null;

        } while ( ! empty($nextEl));

        $this->createSequencesNode($xml, $sequences);

        $this->normalizeIncomingOutgoing($xml, $sequences);

        $processNode = [
            'process' => [
                '_attributes' => [
                    'id' => 'Process_1'
                    , 'isExecutable' => false
                ]
            ]
        ];

        $processNode['process'] = array_merge($processNode['process'], $xml);

        $processNode['bpmndi:BPMNDiagram'] = $this->createXmlLayoutShape($xml);

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
            $a = 0;
        }

        return $this->normalizeXMLString($a);
    }

    /**
     * @param TypeElementAbstract $previousEl
     * @param TypeElementAbstract $actualEl
     * @param TypeElementAbstract $nextEl
     * @param array $sequences
     * @return array
     * @throws \Exception
     */
    private function createNode($previousEl = null, $actualEl, $nextEl = null, array &$sequences): array
    {
        $incoming = $outgoing = null;

        switch (get_class($actualEl)) {
            case StartEvent::class:
                $sourceRef = $actualEl ? $actualEl->getId() : '';
                $targetRef = $nextEl ? $nextEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);
                break;
            case EndEvent::class:
                $sourceRef = $previousEl ? $previousEl->getId() : '';
                $targetRef = $actualEl ? $actualEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);
                break;
            case TaskActivity::class:
                $sourceRef = $previousEl ? $previousEl->getId() : '';
                $targetRef = $actualEl ? $actualEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $incoming = $this->addSequence($sourceRef, $targetRef, $sequences);

                $sourceRef = $actualEl ? $actualEl->getId() : '';
                $targetRef = $nextEl ? $nextEl->getId() : '';
                if ( ! empty($sourceRef) && ! empty($targetRef))
                    $outgoing = $this->addSequence($sourceRef, $targetRef, $sequences);
                break;
            default:
                throw new \Exception('Não existe a classe para o nodo');
        }

        $r = $actualEl->createArrayForXml(
            $incoming ? $incoming->getId() : ''
            , $outgoing ? $outgoing->getId() : ''
        );

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

    private function normalizeIncomingOutgoing(array &$xml, array $sequences): void
    {
        $fn = function (&$item, $k) use ($sequences) {
            /** @var Sequence $s */
            foreach ($sequences as $s) {
                if ($item['_attributes']['id'] == $s->getSourceRef())
                    $item['outgoing'] = $s->getId();
                if ($item['_attributes']['id'] == $s->getTargetRef())
                    $item['incoming'] = $s->getId();
            }
        };
        array_walk($xml['task'], $fn);
        array_walk($xml['endEvent'], $fn);
        array_walk($xml['startEvent'], $fn);
    }

    private function createXmlLayoutShape(array $xml): array
    {
        $xmlLayout = [];

        $sequenceFlow = $xml['sequenceFlow'] ?? null;
        $startEvent = $xml['startEvent'] ?? null;
        $endEvent = $xml['endEvent'] ?? null;
        $task = $xml['task'] ?? null;

        $h = 286; $s = 66;

        if (! empty($sequenceFlow)) {
            array_walk($xml['sequenceFlow'], function ($item, $k) use (&$xmlLayout, &$h, &$s) {
                $x['_attributes']['id'] = $item['_attributes']['id'] . '_gui';
                $x['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $o1 = ['_attributes' => [
                        'x' => $h
                        , 'y' => 202]];
                $o2 = ['_attributes' => [
                        'x' => $h + $s
                        , 'y' => 202]];

                $h += $s + 100;

                $x['waypoint'][] = $o1;
                $x['waypoint'][] = $o2;
                $xmlLayout['BPMNEdge'][] = $x;
            });
        }

        $h = 239; $s = 66;

        if (! empty($startEvent)) {
            array_walk($xml['startEvent'], function ($item, $k) use (&$xmlLayout, &$h, &$s) {
                $el['_attributes']['id'] = $item['_attributes']['id'] . '_di';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $el['Bounds'] = [ '_attributes' => [
                        'x' => $h
                        , 'y' => 184
                        , 'width' => 50
                        , 'height' => 50]];

                $h += 50;

                $xmlLayout['BPMNShape'][] = $el;
            });
        }

        $h += 65;
        $s = 66;

        if (! empty($task)) {
            array_walk($xml['task'], function ($item, $k) use (&$xmlLayout, &$h, &$s) {
                $el['_attributes']['id'] = $item['_attributes']['id'] . '_di';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $el['Bounds'] = [ '_attributes' => [
                    'x' => $h
                    , 'y' => 184
                    , 'width' => 100
                    , 'height' => 80]];

                $h += 50;

                $xmlLayout['BPMNShape'][] = $el;
            });
        }

        $h += 115;
        $s = 66;

        if (! empty($endEvent)) {
            array_walk($xml['endEvent'], function ($item, $k) use (&$xmlLayout, &$h, &$s) {
                $el['_attributes']['id'] = $item['_attributes']['id'] . '_di';
                $el['_attributes']['bpmnElement'] = $item['_attributes']['id'];

                $el['Bounds'] = [ '_attributes' => [
                    'x' => $h
                    , 'y' => 184
                    , 'width' => 50
                    , 'height' => 50]];

                $h += 50;

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

}