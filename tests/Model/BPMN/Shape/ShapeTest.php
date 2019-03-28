<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 27/03/2019 21:09
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\EdgeElement;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\ShapeElement;
use PHPUnit\Framework\TestCase;

class ShapeTest extends TestCase
{
    public function testStartShape()
    {
        $xmlTest = json_decode(file_get_contents('teste.json'), true);

        $shape = new ShapeElement($xmlTest, StartEvent::getNameKey());

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

    public function testEdge()
    {
        $xmlTest = json_decode(file_get_contents('teste.json'), true);

        $edge = new EdgeElement(current($xmlTest['sequenceFlow']));

        $xmlEdge = $edge->xml();

        $expected = [
            'bpmndi:BPMNEdge' => [
                '_attributes' => [
                    'id' => 'sequenceFlow_5c9bfafc3da1c_di'
                    ,'bpmnElement' => 'sequenceFlow_5c9bfafc3da1c'
                ]
                ,'di:waypoint' => [
                    [
                        '_attributes' => [
                            'x' => 50
                            , 'y' => 50
                        ]
                    ]
                    , [
                        '_attributes' => [
                            'x' => 50
                            , 'y' => 50
                        ]
                    ]
                ]
            ]
        ];

        self::assertEquals($expected, $xmlEdge);
    }

}