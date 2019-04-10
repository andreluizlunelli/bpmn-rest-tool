<?php
/**
 * Criado por: andre.lunelli
 * Date: 08/04/2019 - 16:21
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\GetAllSequences;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\ShapeBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Xml\BpmnXmlBuilder;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class ShapeBuilderTest extends TestCase
{
    public function testeLayout()
    {
        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../../bpmn_xml/Project management planNovaTentativa.xml'));
        $bpmn = new BpmnMetadataBuilder($projectEntity);
        $bpmnXmlBuilder = new BpmnXmlBuilder();
        $rootXml = $bpmnXmlBuilder->build($bpmn->buildMetadata());
        $getAllSequences = new GetAllSequences($rootXml);
        $sequences = $getAllSequences->all();
        $calcShape = new CalcShape();
        $shapeBuilder = new ShapeBuilder($rootXml, $sequences, $calcShape);
        $xmlArray = $shapeBuilder->xml();
    }

}
