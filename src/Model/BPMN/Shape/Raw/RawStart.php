<?php
/**
 * Criado por: andre.lunelli
 * Date: 28/03/2019 - 09:12
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw;

use andreluizlunelli\BpmnRestTool\Model\Traits\AttrElement;

class RawStart
{
    use AttrElement;

    private $outgoing;

    public function __construct(string $id, string $name, string $outgoing)
    {
        $this->id = $id;
        $this->name = $name;
        $this->outgoing = $outgoing;
    }

    /**
     * @return string
     */
    public function getOutgoing(): string
    {
        return $this->outgoing;
    }

    /**
     * @param string $outgoing
     * @return RawStart
     */
    public function setOutgoing(string $outgoing): RawStart
    {
        $this->outgoing = $outgoing;
        return $this;
    }

}
