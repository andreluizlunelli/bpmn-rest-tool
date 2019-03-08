<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 21:39
 */

namespace andreluizlunelli\BpmnRestTool\Model\Traits;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementInterface;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

trait CreateFromTask
{
    public static function createFromTask(ProjectTask $task): TypeElementInterface
    {
        return new self($task);
    }

}
