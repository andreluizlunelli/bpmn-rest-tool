<?php

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\SplitSubprocess;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\BpmnBuilderSplitSubprocess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\GetAllElementTypeSubprocess;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class BpmnBuilderSplitSubprocessTest extends TestCase
{
    public function testa()
    {
        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../../bpmn_xml/initiatingplanningclosing.xml'));

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $rootEl = $bpmn->buildMetadata();

        $allSubProcess = (new GetAllElementTypeSubprocess($rootEl))->all();
        $builder = new BpmnBuilderSplitSubprocess();
        $listXmlString = $builder->buildXmlsSplited($allSubProcess);
    }

}