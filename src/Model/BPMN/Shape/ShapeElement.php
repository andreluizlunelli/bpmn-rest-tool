<?php
/**
 * User: André Luiz
 * Date: 27/03/2019
 * Time: 20:50
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw\RawEnd;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw\RawStart;

class ShapeElement
{

    public static $keyShape = 'bpmndi:BPMNShape';

    public function innerXml($id, $bpmnElement, $x, $y, $width, $height, $isExpanded = false): array
    {
        return [
            self::$keyShape => [
                '_attributes' => [
                    'id' => $id
                    ,'bpmnElement' => $bpmnElement
                    ,'isExpanded' => $isExpanded ? 'true' : 'false'
                ]
                ,'dc:Bounds' => [
                    '_attributes' => [
                        'x' => $x
                        ,'y' => $y
                        ,'width' => $width
                        ,'height' => $height
                    ]
                ]
            ]
        ];
    }

    public function xmlFromRawStart(RawStart $rawStart): array
    {
        return $this->innerXml($rawStart->getId().'_di', $rawStart->getId(), 50, 50, 36, 36);
    }

    public function xmlFromRawEnd(RawEnd $rawEnd): array
    {
        return $this->innerXml($rawEnd->getId().'_di', $rawEnd->getId(), 50, 50, 36, 36);
    }

}
