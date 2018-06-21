<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 18/06/2018
 * Time: 23:27
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Traits\CreateFromTask;

class TaskActivity extends TypeElementAbstract
{
    use CreateFromTask;

    public function createArrayForXml()
    {
        return [
            'task' => [
                '_attributes' => [
                    'id' => $this->id
                    , 'name' => $this->name
                ]
            ]
        ];
    }

}