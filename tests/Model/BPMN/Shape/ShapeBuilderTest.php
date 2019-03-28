<?php
/**
 * Criado por: andre.lunelli
 * Date: 28/03/2019 - 08:42
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\ShapeBuilder;
use PHPUnit\Framework\TestCase;
use Spatie\ArrayToXml\ArrayToXml;

class ShapeBuilderTest extends TestCase
{
    public function testa()
    {
        $xmlTest = json_decode(file_get_contents('teste.json'), true);

        $builder = new ShapeBuilder($xmlTest);

        $xml = $builder->xml();

//        $xml = ['loco' => $xml];

        echo "\n";
        echo ArrayToXml::convert(
            $xml
            ,'BPMNDiagram'
        );
        echo "\n";
    }
}
