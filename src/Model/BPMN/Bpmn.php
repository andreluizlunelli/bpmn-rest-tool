<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/05/2018
 * Time: 17:27
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;

class Bpmn
{
    /**
     * @var ProjectEntity
     */
    private $projectEntity;

    public function __construct(ProjectEntity $projectEntity)
    {
        $this->projectEntity = $projectEntity;
    }

    public function createXml(): void
    {
        
    }

}