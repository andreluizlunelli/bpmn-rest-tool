<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 19:38
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\SequenceFlowTag;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class SequenceFlowTagTest extends TestCase
{
    public function testCriarTags()
    {
        $mapper = new ProjectMapper();

        // o arquivo lido somente tem 2 tasks, então deve gerar apenas 1 sequenceFlow
        $projectEntity = $mapper->map(new \SplFileObject('../../bpmn_xml/SequenceFlowTag_TestCriarTags01Project.xml'));

        $tag = SequenceFlowTag::createFromProjectEntity($projectEntity);

        $tagStr = $tag->create();

        self::assertEquals('<sequenceFlow id="SequenceFlow_1" sourceRef="StartEvent_1" targetRef="Task_1" />', $tagStr);
    }
}