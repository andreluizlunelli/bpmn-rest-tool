<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 13:38
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Xml\BpmnXmlBuilder;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;
use PHPUnit\Framework\TestCase;
use Spatie\ArrayToXml\ArrayToXml;

class BpmnXmlBuilderTest extends TestCase
{

    public function teste()
    {
        $projectEntity = (new ProjectEntity())
            ->addTask(new ProjectTask('tarefa 1', 0))
        ;
        $metadataBuilder = new BpmnMetadataBuilder($projectEntity);
        $rootEl = $metadataBuilder->buildMetadata();

        $expectedXml = [
            'sequenceFlow' => [
                [null]
                ,[null]
            ]
            ,'startEvent' => [
                'outgoing' => []
            ]
            ,'task' => [
                'incoming' => []
                ,'outgoing' => []
            ]
            ,'endEvent' => [
                'incoming' => []
            ]
        ];

        $xmlBuilder = new BpmnXmlBuilder();
        $bpmnXml = $xmlBuilder->build($rootEl);

        $this->assertBpmn($expectedXml, $bpmnXml);
    }

    private function assertBpmn(array $expectedXml, array $actualXml): void
    {
        // remove os _atributos

        echo ArrayToXml::convert($expectedXml);
        echo "\n";
        echo ArrayToXml::convert($actualXml);

        self::assertEquals($expectedXml, $actualXml);
    }

}
