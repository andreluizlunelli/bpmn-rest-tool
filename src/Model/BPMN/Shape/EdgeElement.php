<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 27/03/2019 22:11
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;

class EdgeElement
{
    /**
     * @var array
     */
    private $xml;

    public static $keyShape = 'bpmndi:BPMNEdge';

    /**
     * EdgeElement constructor.
     * @param array $xml
     */
    public function __construct($xml)
    {
        if ($xml instanceof Sequence)
            $xml = current($xml->toArrayXml());
        $this->xml = $xml;
    }

    protected function innerXml($id, $bpmnElement, array $x, array $y): array
    {
        return [
            self::$keyShape => [
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

    public function xml(CalcShape $calcShape): array
    {
        $id = $this->xml['_attributes']['id'] ?? null;
        if (empty($id))
            throw new \Exception('Falta o id');

        $p = $calcShape->getxySequence();

        return $this->innerXml($id.'_di', $id, $p[0], $p[1]);
    }


}
