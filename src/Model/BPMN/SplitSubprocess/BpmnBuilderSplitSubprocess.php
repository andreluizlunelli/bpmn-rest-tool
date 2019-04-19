<?php

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\CalcShape;
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
            $process->setOutgoing(EndEvent::createFromTask(new ProjectTask('', 0)));
            CalcShape::clearSumWidth();
            return (new BpmnBuilder($startEl))->buildXml();
        }, $subProcess);
    }

}