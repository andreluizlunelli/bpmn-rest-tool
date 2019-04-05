<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 13:38
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Xml\BpmnXmlBuilder;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;
use PHPUnit\Framework\TestCase;
use Spatie\ArrayToXml\ArrayToXml;

class BpmnXmlBuilderTest extends TestCase
{
    use RemoveHashAttr;

    public function testeTarefaSimples()
    {
        $projectEntity = (new ProjectEntity())
            ->addTask(new ProjectTask('tarefa 1', 0))
        ;
        $metadataBuilder = new BpmnMetadataBuilder($projectEntity);
        $rootEl = $metadataBuilder->buildMetadata();

        $xmlBuilder = new BpmnXmlBuilder();
        $bpmnXml = $xmlBuilder->build($rootEl);

        $actual = ArrayToXml::convert($bpmnXml);
        $expected = '<?xml version="1.0"?>';
        $expected.= "\n";
        $expected.= '<root><sequenceFlow id="sequenceFlow_5ca6506e6f3e6" sourceRef="StartEvent_5ca6506e6e5fd" targetRef="TaskActivity_5ca6506e6d9f1"/><sequenceFlow id="sequenceFlow_5ca6506e6f7d4" sourceRef="TaskActivity_5ca6506e6d9f1" targetRef="EndEvent_5ca6506e6e9cd"/><startEvent id="StartEvent_5ca6506e6e5fd" name=""><outgoing>sequenceFlow_5ca6506e6f3e6</outgoing></startEvent><taskActivity id="TaskActivity_5ca6506e6d9f1" name="tarefa 1"><outgoing>sequenceFlow_5ca6506e6f7d4</outgoing><incoming>sequenceFlow_5ca6506e6f7c0</incoming></taskActivity><endEvent id="EndEvent_5ca6506e6e9cd" name=""><incoming>sequenceFlow_5ca6506e6f9e6</incoming></endEvent></root>';
        $expected.= "\n";

        $actual = self::removeHashAttr($actual);
        $expected = self::removeHashAttr($expected);

        self::assertEquals($expected, $actual);
    }

    public function testeComUmSubProcesso()
    {
        $projectEntity = (new ProjectEntity())
            ->addTask(new ProjectTask('sub processo da tarefa', 0))
            ->addTask(new ProjectTask('tarefa 1', 1))
        ;
        $metadataBuilder = new BpmnMetadataBuilder($projectEntity);
        $rootEl = $metadataBuilder->buildMetadata();

        $xmlBuilder = new BpmnXmlBuilder();
        $bpmnXml = $xmlBuilder->build($rootEl);

        $actual = ArrayToXml::convert($bpmnXml);

        echo $actual;

        $expected = '<?xml version="1.0"?>';
        $expected.= "\n";
        $expected.= '<root><sequenceFlow id="sequenceFlow_5ca6506e6f3e6" sourceRef="StartEvent_5ca6506e6e5fd" targetRef="TaskActivity_5ca6506e6d9f1"/><sequenceFlow id="sequenceFlow_5ca6506e6f7d4" sourceRef="TaskActivity_5ca6506e6d9f1" targetRef="EndEvent_5ca6506e6e9cd"/><startEvent id="StartEvent_5ca6506e6e5fd" name=""><outgoing>sequenceFlow_5ca6506e6f3e6</outgoing></startEvent><taskActivity id="TaskActivity_5ca6506e6d9f1" name="tarefa 1"><outgoing>sequenceFlow_5ca6506e6f7d4</outgoing><incoming>sequenceFlow_5ca6506e6f7c0</incoming></taskActivity><endEvent id="EndEvent_5ca6506e6e9cd" name=""><incoming>sequenceFlow_5ca6506e6f9e6</incoming></endEvent></root>';
        $expected.= "\n";

        $actual = self::removeHashAttr($actual);
        $expected = self::removeHashAttr($expected);

        self::assertEquals($expected, $actual);
    }

}
