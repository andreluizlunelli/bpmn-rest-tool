<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 15:45
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\Project;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class ProjectMapperTest extends TestCase
{

    public function testMapearArquivoProjectEmObjeto()
    {
        $mapper = new ProjectMapper();

        $project = $mapper->map(new \SplFileObject('../../bpmn_xml/Project management plan.xml'));

        self::assertEquals('Project Management for MS Website', $project->getTitle());
        self::assertEquals('Project management plan.xml', $project->getNameFile());
        self::assertCount(128, $project->getTasks());
    }

}