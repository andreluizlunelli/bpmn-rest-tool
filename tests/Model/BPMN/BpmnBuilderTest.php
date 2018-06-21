<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 19/06/2018
 * Time: 00:13
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;
use Spatie\ArrayToXml\ArrayToXml;

class BpmnBuilderTest extends TestCase
{
    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

    protected function setUp()
    {
        parent::setUp();

        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/BpmnMetadataBuilderTest_testBuildMetadataCom3Elementos.xml'));

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $this->rootEl = $bpmn->buildMetadata();
    }

    public function testCriarXmlBpmn()
    {
        $builder = new BpmnBuilder($this->rootEl);

        $xml = $builder->buildXml();

        $expected = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<definitions xmlns="http://www.omg.org/spec/BPMN/20100524/MODEL" xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI" xmlns:omgdi="http://www.omg.org/spec/DD/20100524/DI" xmlns:omgdc="http://www.omg.org/spec/DD/20100524/DC" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <process id="Process_1" isExecutable="false">
    <sequenceFlow id="SequenceFlow_1" name="" sourceRef="StartEvent_1" targetRef="Task_1" />
    <sequenceFlow id="SequenceFlow_2" sourceRef="Task_1" targetRef="ExclusiveGateway_1" />
    <startEvent id="StartEvent_1" name="Project Management for MS Website">
      <outgoing>SequenceFlow_1</outgoing>
    </startEvent>
    <task id="Task_1" name="Initiating">
      <incoming>SequenceFlow_1</incoming>
      <outgoing>SequenceFlow_2</outgoing>
    </task>
    <exclusiveGateway id="ExclusiveGateway_1" name="Develop Project Charter">
      <incoming>SequenceFlow_2</incoming>
    </exclusiveGateway>
  </process>
</definitions>
EOF;

        self::assertEquals($expected, $xml);
    }

    public function testArrayToXml()
    {
        $result = ArrayToXml::convert($this->rootEl->jsonSerialize(), [
            'rootElementName' => 'definitions'
            , '_attributes' => [
                'xmlns' => 'http://www.omg.org/spec/BPMN/20100524/MODEL'
            ],
        ]);

        ArrayToXml::convert(['task' => [ 0 =>'asdf', 1 => 'qwer']]);

        var_dump($result);
    }

}