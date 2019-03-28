<?php
/**
 * Criado por: andre.lunelli
 * Date: 28/03/2019 - 09:23
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw;

use andreluizlunelli\BpmnRestTool\Model\Traits\AttrElement;

class RawEnd
{
    use AttrElement;

    private $incoming;

    public function __construct(string $id, string $name, string $incoming)
    {
        $this->id = $id;
        $this->name = $name;
        $this->incoming = $incoming;
    }
}
