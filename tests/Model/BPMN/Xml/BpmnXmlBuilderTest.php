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

        $xmlBuilder = new BpmnXmlBuilder();
        $bpmnXml = $xmlBuilder->build($rootEl);

        $actual = ArrayToXml::convert($bpmnXml);
        $expected = '<?xml version="1.0"?>';
        $expected.= "\n";
        $expected.= '<root><sequenceFlow id="sequenceFlow_5ca6506e6f3e6" sourceRef="StartEvent_5ca6506e6e5fd" targetRef="TaskActivity_5ca6506e6d9f1"/><sequenceFlow id="sequenceFlow_5ca6506e6f7d4" sourceRef="TaskActivity_5ca6506e6d9f1" targetRef="EndEvent_5ca6506e6e9cd"/><startEvent id="StartEvent_5ca6506e6e5fd" name=""><outgoing>sequenceFlow_5ca6506e6f3e6</outgoing></startEvent><taskActivity id="TaskActivity_5ca6506e6d9f1" name="tarefa 1"><outgoing>sequenceFlow_5ca6506e6f7d4</outgoing><incoming>sequenceFlow_5ca6506e6f7c0</incoming></taskActivity><endEvent id="EndEvent_5ca6506e6e9cd" name=""><incoming>sequenceFlow_5ca6506e6f9e6</incoming></endEvent></root>';
        $expected.= "\n";

        $actual = self::removeHashAttr($actual);
        $expected = self::removeHashAttr($expected);

        self::assertEquals($expected, $actual);
    }

    public static function removeHashAttr(string $string)
    {
        self::removeAllIds('sequenceFlow_', $string);
        self::removeAllIds('StartEvent_', $string);
        self::removeAllIds('TaskActivity_', $string);
        self::removeAllIds('EndEvent_', $string);
        return $string;
    }

    private static function removeAllIds(string $keySearch, string &$val): void
    {
        while (strlen(self::valReplace($val, $keySearch)) > 0) {
            $remove = self::valReplace($val, $keySearch);
            $val = str_replace($remove, '', $val);
        }
    }

    private static function valReplace(string $string, string $key): string
    {
        $pos = strpos($string, $key);

        if ( ! $pos)
            return '';

        //soma mais 13 da hash
        return substr($string, $pos, strlen($key) + 13);
    }
}
