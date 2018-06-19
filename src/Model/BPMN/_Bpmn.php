<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/05/2018
 * Time: 17:27
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;

class _Bpmn
{
    /**
     * @var ProjectEntity
     */
    private $projectEntity;

    public function __construct(ProjectEntity $projectEntity)
    {
        $this->projectEntity = $projectEntity;
    }

    public function createXml(): string
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n{$this->definitionsTag()}";
    }

    private function definitionsTag()
    {
        $xml = '<definitions xmlns="http://www.omg.org/spec/BPMN/20100524/MODEL" xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" xmlns:omgdi="http://www.omg.org/spec/DD/20100524/DI" xmlns:omgdc="http://www.omg.org/spec/DD/20100524/DC" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="sid-38422fae-e03e-43a3-bef4-bd33b32041b2" targetNamespace="http://bpmn.io/bpmn" exporter="http://bpmn.io" exporterVersion="0.10.1">\n';
        $xml.= $this->processTag();
        $xml.= '</definitions>';

        return $xml;
    }

    private function processTag(): string
    {
        $xml = '<process id="Process_1" isExecutable="false">';
        $xml.= $this->sequenceFlowTags();
        $xml.= $this->startEventTag();
        $xml.= $this->tasksTag();
        $xml.= $this->exclusiveGatewayTag();
        $xml.= $this->endEventTag();
        $xml.= '</process>';

        return $xml;
    }

    private function sequenceFlowTags(): string
    {
    }

    private function startEventTag(): string
    {
    }

    private function tasksTag(): string
    {
    }

    private function exclusiveGatewayTag(): string
    {
    }

    private function endEventTag(): string
    {
    }

}