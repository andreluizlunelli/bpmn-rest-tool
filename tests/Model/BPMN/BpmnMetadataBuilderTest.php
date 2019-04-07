<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/05/2018
 * Time: 17:26
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

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
            , 'name' => ''
            , 'outgoing' => [
                'type' => 'SubProcess'
                , 'name' => 'Project Management for MS Website'
                , 'outgoing' => [
                    'type' => 'EndEvent'
                    , 'name' => ''
                    , 'outgoing' => null
                ]
                , 'subprocess' => [
                    'type' => 'StartEvent'
                    , 'name' => ''
                    , 'outgoing' => [
                        'type' => 'SubProcess'
                        , 'name' => 'Initiating'
                        , 'outgoing' => [
                            'type' => 'EndEvent'
                            , 'name' => ''
                            , 'outgoing' => null
                        ]
                        , 'subprocess' => [
                            'type' => 'StartEvent'
                            , 'name' => ''
                            , 'outgoing' => [
                                'type' => 'SubProcess'
                                , 'name' => 'Develop Project Charter'
                                , 'outgoing' => [
                                    'type' => 'SubProcess'
                                    , 'name' => 'Develop Preliminary Project Scope Statement'
                                    , 'outgoing' => [
                                        'type' => 'EndEvent'
                                        , 'name' => ''
                                        , 'outgoing' => null
                                    ]
                                    , 'subprocess' => [
                                        'type' => 'StartEvent'
                                        , 'name' => ''
                                        , 'outgoing' => [
                                            'type' => 'TaskActivity'
                                            , 'name' => 'Conduct Planning Workshop'
                                            , 'outgoing' => [
                                                'type' => 'EndEvent'
                                                , 'name' => ''
                                                , 'outgoing' => null
                                            ]
                                        ]
                                    ]
                                ]
                                , 'subprocess' => [
                                    'type' => 'StartEvent'
                                    , 'name' => ''
                                    , 'outgoing' => [
                                        'type' => 'TaskActivity'
                                        , 'name' => 'Identify Goals and Objectives'
                                        , 'outgoing' => [
                                            'type' => 'TaskActivity'
                                            , 'name' => 'Develop Strategies and Plans'
                                            , 'outgoing' => [
                                                'type' => 'TaskActivity'
                                                , 'name' => 'Research Previous Experience'
                                                , 'outgoing' => [
                                                    'type' => 'EndEvent'
                                                    , 'name' => ''
                                                    , 'outgoing' => null
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

    public function testBuildMetadata2()
    {
        $this->projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/initiatingplanningclosing.xml'));

        $bpmn = new BpmnMetadataBuilder($this->projectEntity);

        $actual = $bpmn->buildMetadata();

        $expected = [
            'type' => 'StartEvent'
            , 'name' => ''
            , 'outgoing' => [
                'type' => 'SubProcess'
                , 'name' => 'Project Management for MS Website'
                , 'outgoing' => [
                    'type' => 'EndEvent'
                    , 'name' => ''
                    , 'outgoing' => null
                ]
                , 'subprocess' => [
                    'type' => 'StartEvent'
                    , 'name' => ''
                    , 'outgoing' => [
                        'type' => 'SubProcess'
                        , 'name' => 'Initiating'
                        , 'outgoing' => [
                            'type' => 'SubProcess'
                            , 'name' => 'Planning'
                            , 'outgoing' => [
                                'type' => 'SubProcess'
                                , 'name' => 'Closing'
                                , 'outgoing' => [
                                    'type' => 'EndEvent'
                                    , 'name' => ''
                                    , 'outgoing' => null
                                ]
                                , 'subprocess' => [
                                    'type' => 'StartEvent'
                                    , 'name' => ''
                                    , 'outgoing' => [
                                        'type' => 'SubProcess'
                                        , 'name' => 'Close Project'
                                        , 'outgoing' => [
                                            'type' => 'SubProcess'
                                            , 'name' => 'Contract Closure'
                                            , 'outgoing' => [
                                                'type' => 'EndEvent'
                                                , 'name' => ''
                                                , 'outgoing' => null
                                            ]
                                            , 'subprocess' => [
                                                'type' => 'StartEvent'
                                                , 'name' => ''
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Close Contract'
                                                    , 'outgoing' => [
                                                        'type' => 'EndEvent'
                                                        , 'name' => ''
                                                        , 'outgoing' => null
                                                    ]
                                                ]
                                            ]
                                        ]
                                        , 'subprocess' => [
                                            'type' => 'StartEvent'
                                            , 'name' => ''
                                            , 'outgoing' => [
                                                'type' => 'TaskActivity'
                                                , 'name' => 'Assess Satisfaction'
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Summarize Project Results and Lessons Learned'
                                                    , 'outgoing' => [
                                                        'type' => 'TaskActivity'
                                                        , 'name' => 'Review and Recognize Team Performance'
                                                        , 'outgoing' => [
                                                            'type' => 'TaskActivity'
                                                            , 'name' => 'Close Out the Project Records'
                                                            , 'outgoing' => [
                                                                'type' => 'TaskActivity'
                                                                , 'name' => 'Review and Reconcile Financial Performance'
                                                                , 'outgoing' => [
                                                                    'type' => 'EndEvent'
                                                                    , 'name' => ''
                                                                    , 'outgoing' => null
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                            , 'subprocess' => [
                                'type' => 'StartEvent'
                                , 'name' => ''
                                , 'outgoing' => [
                                    'type' => 'SubProject'
                                    , 'name' => 'Set Up Project Environment'
                                    , 'outgoing' => [
                                        'type' => 'SubProcess'
                                        , 'name' => 'Define Scope'
                                        , 'outgoing' => [
                                            'type' => 'SubProcess'
                                            , 'name' => 'Develop Project Schedule'
                                            , 'outgoing' => [
                                                'type' => 'EndEvent'
                                                , 'name' => ''
                                                , 'outgoing' => null
                                            ]
                                            , 'subprocess' => [
                                                'type' => 'StartEvent'
                                                , 'name' => ''
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Build Work Breakdown Structure'
                                                    , 'outgoing' => [
                                                        'type' => 'TaskActivity'
                                                        , 'name' => 'Develop Resource Plans'
                                                        , 'outgoing' => [
                                                            'type' => 'TaskActivity'
                                                            , 'name' => 'Prepare Project Estimates'
                                                            , 'outgoing' => [
                                                                'type' => 'TaskActivity'
                                                                , 'name' => 'Define Dependencies and Develop Project Schedule'
                                                                , 'outgoing' => [
                                                                    'type' => 'TaskActivity'
                                                                    , 'name' => 'Document Assumptions'
                                                                    , 'outgoing' => [
                                                                        'type' => 'EndEvent'
                                                                        , 'name' => ''
                                                                        , 'outgoing' => null
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                        , 'subprocess' => [
                                            'type' => 'StartEvent'
                                            , 'name' => ''
                                            , 'outgoing' => [
                                                'type' => 'TaskActivity'
                                                , 'name' => 'Document Scope Management Plan'
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Specify Deliverables and Acceptance Criteria'
                                                    , 'outgoing' => [
                                                        'type' => 'TaskActivity'
                                                        , 'name' => 'Define Scope'
                                                        , 'outgoing' => [
                                                            'type' => 'TaskActivity'
                                                            , 'name' => 'Document Assumptions'
                                                            , 'outgoing' => [
                                                                'type' => 'EndEvent'
                                                                , 'name' => ''
                                                                , 'outgoing' => null
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                    , 'subprocess' => [
                                        'type' => 'StartEvent'
                                        , 'name' => ''
                                        , 'outgoing' => [
                                            'type' => 'TaskActivity'
                                            , 'name' => 'Prepare Facilities'
                                            , 'outgoing' => [
                                                'type' => 'TaskActivity'
                                                , 'name' => 'Set Up Project Standards and Procedures'
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Set Up Project Management Tools'
                                                    , 'outgoing' => [
                                                        'type' => 'TaskActivity'
                                                        , 'name' => 'Set Up Project Book'
                                                        , 'outgoing' => [
                                                            'type' => 'EndEvent'
                                                            , 'name' => ''
                                                            , 'outgoing' => null
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                        , 'subprocess' => [
                            'type' => 'StartEvent'
                            , 'name' => ''
                            , 'outgoing' => [
                                'type' => 'SubProcess'
                                , 'name' => 'Develop Project Charter'
                                , 'outgoing' => [
                                    'type' => 'SubProcess'
                                    , 'name' => 'Develop Preliminary Project Scope Statement'
                                    , 'outgoing' => [
                                        'type' => 'EndEvent'
                                        , 'name' => ''
                                        , 'outgoing' => null
                                    ]
                                    , 'subprocess' => [
                                        'type' => 'StartEvent'
                                        , 'name' => ''
                                        , 'outgoing' => [
                                            'type' => 'TaskActivity'
                                            , 'name' => 'Conduct Planning Workshop'
                                            , 'outgoing' => [
                                                'type' => 'TaskActivity'
                                                , 'name' => 'Document Project Costs and Benefits'
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Develop High Level Work Breakdown Structure'
                                                    , 'outgoing' => [
                                                        'type' => 'TaskActivity'
                                                        , 'name' => 'Conduct Peer Review'
                                                        , 'outgoing' => [
                                                            'type' => 'TaskActivity'
                                                            , 'name' => 'Prepare Preliminary Project Scope Statement'
                                                            , 'outgoing' => [
                                                                'type' => 'EndEvent'
                                                                , 'name' => ''
                                                                , 'outgoing' => null
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                                , 'subprocess' => [
                                    'type' => 'StartEvent'
                                    , 'name' => ''
                                    , 'outgoing' => [
                                        'type' => 'TaskActivity'
                                        , 'name' => 'Identify Goals and Objectives'
                                        , 'outgoing' => [
                                            'type' => 'TaskActivity'
                                            , 'name' => 'Develop Strategies and Plans'
                                            , 'outgoing' => [
                                                'type' => 'TaskActivity'
                                                , 'name' => 'Research Previous Experience'
                                                , 'outgoing' => [
                                                    'type' => 'TaskActivity'
                                                    , 'name' => 'Develop Project Charter'
                                                    , 'outgoing' => [
                                                        'type' => 'EndEvent'
                                                        , 'name' => ''
                                                        , 'outgoing' => null
                                                    ]
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