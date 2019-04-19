<?php

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

class BpmnBuilderSplitSubprocess
{
    /**
     * @param array $subProcess Subprocess
     * @return array strings
     *
     * @see SubProcess
     */
    public function buildXmlsSplited(array $subProcess): array
    {
        return array_map(function (SubProcess $process) {
            $startEl = StartEvent::createFromTask(new ProjectTask('', 0));
            $startEl->setOutgoing($process);
            return (new BpmnBuilder($startEl))->buildXml();
        }, $subProcess);
    }

}