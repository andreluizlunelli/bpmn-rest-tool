<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 21:39
 */

namespace andreluizlunelli\BpmnRestTool\Model\Traits;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

trait CreateFromTask
{
    public static function createFromTask(ProjectTask $task): self
    {
        return new self($task->getId(), $task->getName(), $task->getStartDate(), $task->getFinishDate());
    }

}