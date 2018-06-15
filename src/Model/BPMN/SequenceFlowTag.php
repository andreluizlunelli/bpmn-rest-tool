<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 19:45
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;

class SequenceFlowTag implements TagCreator
{
    /**
     * @var ProjectEntity
     */
    private $projectEntity;

    public static function createFromProjectEntity(ProjectEntity $projectEntity): self
    {
        return (new SequenceFlowTag())
            ->setProjectEntity($projectEntity);
    }

    public function create(): string
    {

    }

    /**
     * @return ProjectEntity
     */
    public function getProjectEntity(): ProjectEntity
    {
        return $this->projectEntity;
    }

    /**
     * @param ProjectEntity $projectEntity
     * @return SequenceFlowTag
     */
    public function setProjectEntity(ProjectEntity $projectEntity): SequenceFlowTag
    {
        $this->projectEntity = $projectEntity;
        return $this;
    }

}