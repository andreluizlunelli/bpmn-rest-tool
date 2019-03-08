<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 20:48
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;
use andreluizlunelli\BpmnRestTool\Model\Traits\AttrElement;

abstract class TypeElementAbstract implements TypeElementInterface
{
    use AttrElement;

    /**
     * @var self
     */
    protected $outgoing;

    /**
     * @var ProjectTask
     */
    public $projectTask;

    public function __construct(ProjectTask $projectTask, ?self $outgoing = null)
    {
        $this->projectTask = $projectTask;
        $this->id = "{$this->getNameWithoutNamespace()}_{$projectTask->getId()}";
        $this->name = $projectTask->getName();
        $this->outgoing = $outgoing;
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->getNameWithoutNamespace()
            , 'id' => $this->id
            , 'name' => $this->name
            , 'outgoing' => $this->outgoing
        ];
    }

    public function getOutgoing(): ?self
    {
        return $this->outgoing;
    }

    public function setOutgoing(self $outgoing): self
    {
        $this->outgoing = $outgoing;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getNameWithoutNamespace(): string
    {
        $arr = explode('\\', get_class($this));

        return array_pop($arr);
    }

}
