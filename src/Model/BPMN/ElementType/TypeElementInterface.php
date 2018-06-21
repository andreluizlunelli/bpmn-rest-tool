<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 20:47
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

interface TypeElementInterface extends \JsonSerializable
{
    public static function createFromTask(ProjectTask $task): TypeElementInterface;

    public function createArrayForXml();

}