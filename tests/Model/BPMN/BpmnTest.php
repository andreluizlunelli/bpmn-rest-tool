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
            ->map(new \SplFileObject('../../bpmn_xml/Project management plan.xml'));
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

        $expected = [
            'type' => 'StartEvent'
            , 'id' => 'StartEvent_1'
            , 'name' => 'Project Management for MS Website'
            , 'outgoing' => [[
                'type' => 'EndEvent'
                , 'id' => 'EndEvent_2'
                , 'name' => 'Initiating'
            ]]
        ];

        self::assertEquals($expected, $actual);
    }

}