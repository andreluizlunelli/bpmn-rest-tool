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

    public function testBuildMetadata()
    {
        // OutlineLevel ele traz o nível de identação das tarefas pra identificar os subprocessos

        $this->projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/Project management planModificado.xml'));

        $bpmn = new BpmnMetadataBuilder($this->projectEntity);

        $actual = $bpmn->buildMetadata();

        $expected = [
            'type' => 'StartEvent'
            ,'name' => ''
            ,'outgoing' => [
                'type' => 'SubProcess'
                ,'name' => 'Project Management for MS Website'
                ,'outgoing' => [
                    'type' => 'EndEvent'
                    ,'name' => ''
                    ,'outgoing' => null
                ]
                ,'subprocess' => [
                    'type' => 'StartEvent'
                    ,'name' => ''
                    ,'outgoing' => [
                        'type' => 'SubProcess'
                        ,'name' => 'Initiating'
                        ,'outgoing' => [
                            'type' => 'EndEvent'
                            ,'name' => ''
                            ,'outgoing' => null
                        ]
                        ,'subprocess' => [
                            'type' => 'StartEvent'
                            ,'name' => ''
                            ,'outgoing' => [
                                'type' => 'SubProcess'
                                ,'name' => 'Develop Project Charter'
                                ,'outgoing' => [
                                    'type' => 'SubProcess'
                                    ,'name' => 'Develop Preliminary Project Scope Statement'
                                    ,'outgoing' => [
                                        'type' => 'EndEvent'
                                        ,'name' => ''
                                        ,'outgoing' => null
                                    ]
                                    ,'subprocess' => [
                                        'type' => 'StartEvent'
                                        ,'name' => ''
                                        ,'outgoing' => [
                                            'type' => 'TaskActivity'
                                            ,'name' => 'Conduct Planning Workshop'
                                            ,'outgoing' => [
                                                'type' => 'EndEvent'
                                                ,'name' => ''
                                                ,'outgoing' => null
                                            ]
                                        ]
                                    ]
                                ]
                                ,'subprocess' => [
                                    'type' => 'StartEvent'
                                    ,'name' => ''
                                    ,'outgoing' => [
                                        'type' => 'TaskActivity'
                                        ,'name' => 'Identify Goals and Objectives'
                                        ,'outgoing' => [
                                            'type' => 'TaskActivity'
                                            ,'name' => 'Develop Strategies and Plans'
                                            ,'outgoing' => [
                                                'type' => 'TaskActivity'
                                                ,'name' => 'Research Previous Experience'
                                                ,'outgoing' => [
                                                    'type' => 'EndEvent'
                                                    ,'name' => ''
                                                    ,'outgoing' => null
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ]

                ],
            ]
        ];

        self::assertEquals($expected, $actual->jsonSerialize());
    }

}
