<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 21:48
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Traits\CreateFromTask;

class EndEvent extends TypeElementAbstract
{
    use CreateFromTask;

    public function createArrayForXml()
    {
        return [
            'endEvent' => [
                '_attributes' => [
                    'id' => $this->id
                    , 'name' => $this->name
                ]
            ]
        ];
    }

}