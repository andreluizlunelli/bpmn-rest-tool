<?php
/**
 * User: André Luiz
 * Date: 27/03/2019
 * Time: 20:50
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;

class ShapeStartEvent
{
    use ShapeTrait;

    private $xml;

    /**
     * ShapeStartEvent constructor.
     * @param $xml
     */
    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    public function xml(): array
    {
        $startEvent = $this->xml[StartEvent::getNameKey()];
        $id = $startEvent['_attributes']['id'] ?? null;
        if (empty($id))
            throw new \Exception('Falta o id');

        return $this->innerXml($id.'_di', $id, 50, 50, 36, 36);
    }

}