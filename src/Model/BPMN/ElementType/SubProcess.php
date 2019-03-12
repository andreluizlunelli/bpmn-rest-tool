<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 18/06/2018
 * Time: 23:27
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Traits\CreateFromTask;

class SubProcess extends TypeElementAbstract
{
    use CreateFromTask;

    /**
     * @var TypeElementAbstract
     */
    private $subprocess;

    public function createArrayForXml(string $incoming = '', string $outgoing = ''): array
    {
        $key = lcfirst($this->getNameWithoutNamespace());
        $xmlArray = [
            $key => [
                '_attributes' => [
                    'id' => $this->id
                    , 'name' => $this->name
                ]
            ]
        ];

        if ( ! empty($incoming))
            $xmlArray[$key]['incoming'] = $incoming;

        if ( ! empty($outgoing))
            $xmlArray[$key]['outgoing'] = $outgoing;

        return $xmlArray;
    }

    /**
     * @return TypeElementAbstract
     */
    public function getSubprocess(): ?TypeElementAbstract
    {
        return $this->subprocess;
    }

    /**
     * @param TypeElementAbstract $subprocess
     * @return SubProcess
     */
    public function setSubprocess(TypeElementAbstract $subprocess): self
    {
        $this->subprocess = $subprocess;
        return $this;
    }

    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();
        $arr['subprocess'] = $this->subprocess ? $this->subprocess->jsonSerialize() : null;
        return $arr;
    }

}
