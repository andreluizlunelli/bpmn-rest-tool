<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/05/2018
 * Time: 17:26
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\_Bpmn;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class BpmnMetadataBuilderTest extends TestCase
{
    /**
     * @var ProjectEntity
     */
    private $projectEntity;

    protected function setUp()
    {
        parent::setUp();

        $this->projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/BpmnMetadataBuilderTest_testBuildMetadata.xml'));
    }

    public function testBuildMetadata2()
    {
        $bpmn = new BpmnMetadataBuilder($this->projectEntity);

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

    public function testBuildMetadataCom3Elementos()
    {
        $this->projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/BpmnMetadataBuilderTest_testBuildMetadataCom3Elementos.xml'));

        $bpmn = new BpmnMetadataBuilder($this->projectEntity);

        $actual = $bpmn->buildMetadata();

        $tasks = $this->projectEntity->getTasks();

        $expected = [
            'type' => 'StartEvent'
            , 'id' => current($tasks)->getId()
            , 'name' => 'Project Management for MS Website'
            , 'startDate' => '2018-05-16 08:00:00'
            , 'finishDate' => '2018-09-10 17:00:00'
            , 'outgoing' => [[
                'type' => 'TaskActivity'
                , 'id' => next($tasks)->getId()
                , 'name' => 'Initiating'
                , 'startDate' => '2018-05-16 08:00:00'
                , 'finishDate' => '2018-05-25 12:00:00'
                , 'outgoing' => [[
                    'type' => 'EndEvent'
                    , 'id' => next($tasks)->getId()
                    , 'name' => 'Develop Project Charter'
                    , 'startDate' => '2018-05-16 08:00:00'
                    , 'finishDate' => '2018-05-21 12:00:00'
                    , 'outgoing' => []
                ]]
            ]]
        ];

        self::assertEquals($expected, $actual->jsonSerialize());
    }

    public function testBuildMetadata()
    {
        // OutlineLevel ele traz o nível de identação das tarefas pra identificar os subprocessos

        $this->projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/Project management planModificado.xml'));

        $bpmn = new BpmnMetadataBuilder($this->projectEntity);

        $actual = $bpmn->buildMetadata();

        $expected = [
            'type' => 'StartEvent' // único momento que existe o StartEvent nesse contexto
            ,'name' => ''
            ,'outgoing' => [
                'type' => 'SubProcess'
                ,'name' => 'Project Management for MS Website'
                ,'outgoing' => null
                ,'subprocess' => [
                    'type' => 'SubProcess'
                    ,'name' => 'Initiating'
                    ,'outgoing' => null
                    ,'subprocess' => [
                        'type' => 'SubProcess'
                        ,'name' => 'Develop Project Charter'
                        ,'outgoing' => [
                            'type' => 'SubProcess'
                            ,'name' => 'Develop Preliminary Project Scope Statement'
                            ,'outgoing' => null
                            ,'subprocess' => [
                                'type' => 'TaskActivity'
                                ,'name' => 'Conduct Planning Workshop'
                                ,'outgoing' => null
                            ]
                        ]
                        ,'subprocess' => [
                            'type' => 'TaskActivity'
                            ,'name' => 'Identify Goals and Objectives'
                            ,'outgoing' => [
                                'type' => 'TaskActivity'
                                ,'name' => 'Develop Strategies and Plans'
                                ,'outgoing' => [
                                    'type' => 'TaskActivity'
                                    ,'name' => 'Research Previous Experience'
                                    ,'outgoing' => null
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        self::assertEquals($expected, $actual->jsonSerialize());
    }

}
