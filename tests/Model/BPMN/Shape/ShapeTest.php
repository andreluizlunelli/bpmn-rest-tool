<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 27/03/2019 21:09
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\ShapeStartEvent;
use PHPUnit\Framework\TestCase;

class ShapeTest extends TestCase
{
    public function testStartShape()
    {
        $test = json_decode(file_get_contents('teste.json'), true);

        $shape = new ShapeStartEvent($test);

        $xmlShape = $shape->xml();

        $expected = [
            'bpmndi:BPMNShape' => [
                '_attributes' => [
                    'id' => 'StartEvent_5c9bfafbd2bee_di'
                    ,'bpmnElement' => 'StartEvent_5c9bfafbd2bee'
                ]
                ,'dc:Bounds' => [
                    '_attributes' => [
                        'x' => 50
                        ,'y' => 50
                        ,'width' => 36
                        ,'height' => 36
                    ]
                ]
            ]
        ];

        self::assertEquals($expected, $xmlShape);
    }

}