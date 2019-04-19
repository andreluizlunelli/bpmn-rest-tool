<?php

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\CalcShape;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\ShapeBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Xml\BpmnXmlBuilder;
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
        $this->rootXml = [];
        $bpmnXmlBuilder = new BpmnXmlBuilder();
        $this->rootXml = $bpmnXmlBuilder->build($this->rootEl);
        $getAllSequences = new GetAllSequences($this->rootXml);
        $sequences = $getAllSequences->all();

        $processNode = [
            'process' => [
                '_attributes' => [
                    'id' => 'Process_1'
                    , 'isExecutable' => false
                ]
            ]
        ];

        $processNode['process'] = array_merge($processNode['process'], $this->rootXml);
        $processNode['bpmndi:BPMNDiagram']['_attributes']['id'] = 'BpmnDiagram_1';
        $processNode['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane'] = (new ShapeBuilder($this->rootXml, $sequences, new CalcShape()))->xml();
        $processNode['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane']['_attributes']['id'] = 'BpmnPlane_1';
        $processNode['bpmndi:BPMNDiagram']['bpmndi:BPMNPlane']['_attributes']['bpmnElement'] = 'Process_1';

        try {
            $a = ArrayToXml::convert($processNode, [
                'rootElementName' => 'definitions'
                , '_attributes' => [
                    'xmlns' => "http://www.omg.org/spec/BPMN/20100524/MODEL"
                    , 'xmlns:bpmn' => "http://www.omg.org/spec/BPMN/20100524/MODEL"
                    , 'xmlns:bpmndi' => "http://www.omg.org/spec/BPMN/20100524/DI"
                    , 'xmlns:omgdi' => "http://www.omg.org/spec/DD/20100524/DI"
                    , 'xmlns:omgdc' => "http://www.omg.org/spec/DD/20100524/DC"
                    , 'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance"
                    , 'xmlns:dc' => "http://www.omg.org/spec/DD/20100524/DC"
                    , 'xmlns:di' => "http://www.omg.org/spec/DD/20100524/DI"
                ],
            ]);
        } catch (\DOMException $e) {
            throw $e;
        }

        return $this->normalizeXMLString($a);
    }

    private function normalizeXMLString(string $a): string
    {
        $a = str_replace('<BPMNEdge', '<bpmndi:BPMNEdge', $a);
        $a = str_replace('</BPMNEdge>', '</bpmndi:BPMNEdge>', $a);

        $a = str_replace('<waypoint', '<omgdi:waypoint', $a);

        $a = str_replace('<BPMNShape', '<bpmndi:BPMNShape', $a);
        $a = str_replace('</BPMNShape>', '</bpmndi:BPMNShape>', $a);

        $a = str_replace('<Bounds', '<omgdc:Bounds', $a);

        $a = str_replace('<definitions', '<bpmn:definitions', $a);
        $a = str_replace('</definitions>', '</bpmn:definitions>', $a);

        $a = str_replace('<process', '<bpmn:process', $a);
        $a = str_replace('</process>', '</bpmn:process>', $a);

        $a = str_replace('<startEvent', '<bpmn:startEvent', $a);
        $a = str_replace('</startEvent>', '</bpmn:startEvent>', $a);

        $a = str_replace('<subProcess', '<bpmn:subProcess', $a);
        $a = str_replace('</subProcess>', '</bpmn:subProcess>', $a);

        $a = str_replace('<endEvent', '<bpmn:endEvent', $a);
        $a = str_replace('</endEvent>', '</bpmn:endEvent>', $a);

        $a = str_replace('<incoming', '<bpmn:incoming', $a);
        $a = str_replace('</incoming>', '</bpmn:incoming>', $a);

        $a = str_replace('<outgoing', '<bpmn:outgoing', $a);
        $a = str_replace('</outgoing>', '</bpmn:outgoing>', $a);

        $a = str_replace('<sequenceFlow', '<bpmn:sequenceFlow', $a);

        $a = str_replace('<taskActivity', '<bpmn:task', $a);
        $a = str_replace('</taskActivity>', '</bpmn:task>', $a);

        return $a;
    }

}
