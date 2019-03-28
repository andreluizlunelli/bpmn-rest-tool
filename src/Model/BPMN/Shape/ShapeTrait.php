<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 27/03/2019 21:33
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

trait ShapeTrait
{
    protected function innerXml($id, $bpmnElement, $x, $y, $width, $height): array
    {
        return [
            'bpmndi:BPMNShape' => [
                '_attributes' => [
                    'id' => $id
                    ,'bpmnElement' => $bpmnElement
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
}