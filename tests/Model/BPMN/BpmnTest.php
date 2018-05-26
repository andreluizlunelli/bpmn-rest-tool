<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/05/2018
 * Time: 17:26
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Bpmn;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class BpmnTest extends TestCase
{
    public function testCriarBpmnXml()
    {
        $mapper = new ProjectMapper();

        $projectEntity = $mapper->map(new \SplFileObject('../../bpmn_xml/Project management plan.xml'));

        $bpmn = new Bpmn($projectEntity);
        $bpmn->createXml();
    }
}