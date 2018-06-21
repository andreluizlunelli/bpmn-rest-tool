<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 20:48
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Traits\CreateFromTask;

class StartEvent extends TypeElementAbstract
{
    use CreateFromTask;

    public function createArrayForXml()
    {
        return [
            'startEvent' => [
                '_attributes' => [
                    'id' => $this->id
                    , 'name' => $this->name
                ]
            ]
        ];
    }

}