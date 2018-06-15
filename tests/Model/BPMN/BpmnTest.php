<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/05/2018
 * Time: 17:26
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Bpmn;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class BpmnTest extends TestCase
{
    /**
     * @var ProjectEntity
     */
    private $projectEntity;

    protected function setUp()
    {
        parent::setUp();

        $this->projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/SequenceFlowTag_TestCriarTags01Project.xml'));
    }

    public function testCriarBpmnXml()
    {
        $mapper = new ProjectMapper();

        $projectEntity = $mapper->map(new \SplFileObject('../../bpmn_xml/Project management plan.xml'));

        $bpmn = new Bpmn($projectEntity);
        $bpmn->createXml();
    }

    public function testBuildMetadata()
    {
        $bpmn = new BpmnBuilder($this->projectEntity);

        $actual = $bpmn->buildMetadata();

        $tasks = $this->projectEntity->getTasks();

        $expected = [
            'type' => 'StartEvent'
            , 'id' => current($tasks)->getId()
            , 'name' => 'Project Management for MS Website'
            , 'startDate' => '2018-05-16 08:00:00'
            , 'finishDate' => '2018-09-10 17:00:00'
            , 'outgoing' => [[
                'type' => 'EndEvent'
                , 'id' => next($tasks)->getId()
                , 'name' => 'Initiating'
                , 'startDate' => '2018-05-16 08:00:00'
                , 'finishDate' => '2018-05-25 12:00:00'
                , 'outgoing' => []
            ]]
        ];

        self::assertEquals($expected, $actual->jsonSerialize());
    }

}