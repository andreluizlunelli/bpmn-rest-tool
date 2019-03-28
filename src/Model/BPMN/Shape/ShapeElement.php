<?php
/**
 * User: André Luiz
 * Date: 27/03/2019
 * Time: 20:50
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

class ShapeElement
{
    /**
     * @var array
     */
    private $xml;

    /**
     * @var string
     */
    private $keyName;

    /**
     * ShapeElement constructor.
     * @param array $xml
     * @param string $keyName
     */
    public function __construct(array $xml, string $keyName)
    {
        $this->xml = $xml;
        $this->keyName = $keyName;
    }

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

    public function xml(): array
    {
        $startEvent = $this->xml[$this->keyName];
        $id = $startEvent['_attributes']['id'] ?? null;
        if (empty($id))
            throw new \Exception('Falta o id');

        return $this->innerXml($id.'_di', $id, 50, 50, 36, 36);
    }

}