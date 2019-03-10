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
        $xmlArray = [
            'SubProcess' => [
                '_attributes' => [
                    'id' => $this->id
                    , 'name' => $this->name
                ]
            ]
        ];

        if ( ! empty($incoming))
            $xmlArray['SubProcess']['incoming'] = $incoming;

        if ( ! empty($outgoing))
            $xmlArray['SubProcess']['outgoing'] = $outgoing;

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

}
