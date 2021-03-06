<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 21:39
 */

namespace andreluizlunelli\BpmnRestTool\Model\Traits;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

trait CreateFromTask
{
    public static function createFromTask(ProjectTask $task): TypeElementAbstract
    {
        return new self($task);
    }

    public static function getNameKey(): string
    {
        $arr = explode('\\', self::class);

        return lcfirst( array_pop($arr) );
    }
}
