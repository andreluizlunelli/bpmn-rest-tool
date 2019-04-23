<?php

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\SplitSubprocess;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\GetAllElementTypeSubprocess;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class GetAllElementTypeSubprocessTest extends TestCase
{

    public function testeCriarListaSubTarefas()
    {
        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../../bpmn_xml/initiatingplanningclosing.xml'));

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $rootEl = $bpmn->buildMetadata();

        $allSubProcess = (new GetAllElementTypeSubprocess($rootEl))->all();

        self::assertCount(11, $allSubProcess);
    }

}