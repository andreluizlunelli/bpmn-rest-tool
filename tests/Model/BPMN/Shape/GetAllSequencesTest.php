<?php
/**
 * Criado por: andre.lunelli
 * Date: 05/04/2019 - 14:10
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\GetAllSequences;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Xml\BpmnXmlBuilder;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class GetAllSequencesTest extends TestCase
{
    public function teste()
    {
        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../../bpmn_xml/Project management planModificado_teste2.xml'));

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $rootEl = $bpmn->buildMetadata();

        $bpmnXmlBuilder = new BpmnXmlBuilder();

        $xmlProcess = $bpmnXmlBuilder->build($rootEl);

        $allSequences = new GetAllSequences($xmlProcess);
        $all = $allSequences->all(); // retorna um array do tipo Sequence

        self::assertCount(4, $all);
    }
}
