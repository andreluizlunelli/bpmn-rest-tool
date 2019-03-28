<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 27/03/2019 22:11
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

class EdgeElement
{
    /**
     * @var array
     */
    private $xml;

    /**
     * EdgeElement constructor.
     * @param array $xml
     */
    public function __construct(array $xml)
    {
        $this->xml = $xml;
    }

    protected function innerXml($id, $bpmnElement, array $x, array $y): array
    {
        return [
            'bpmndi:BPMNEdge' => [
                '_attributes' => [
                    'id' => $id
                    , 'bpmnElement' => $bpmnElement
                ]
                , 'di:waypoint' => [
                    [
                        '_attributes' => [
                            'x' => $x[0]
                            , 'y' => $y[0]
                        ]
                    ]
                    , [
                        '_attributes' => [
                            'x' => $x[1]
                            , 'y' => $y[1]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function xml(): array
    {
        $id = $this->xml['_attributes']['id'] ?? null;
        if (empty($id))
            throw new \Exception('Falta o id');

        return $this->innerXml($id.'_di', $id, [50,50], [50,50]);
    }


}