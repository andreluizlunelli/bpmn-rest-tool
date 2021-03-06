<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 20:48
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;
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

    /**
     * @var TypeElementAbstract
     */
    private $prevEl;

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
            , 'name' => $this->name
            , 'outgoing' => $this->outgoing ? $this->outgoing->jsonSerialize() : null
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
    protected function getNameWithoutNamespace(): string
    {
        $arr = explode('\\', get_class($this));

        return array_pop($arr);
    }

    public abstract static function getNameKey(): string;

    public function newCreateArrayForXml(?Sequence $incoming, ?Sequence $outgoing): array
    {
        $key = lcfirst($this->getNameWithoutNamespace());
        $xmlArray = [
            $key => [
                '_attributes' => [
                    'id' => $this->id
                    , 'name' => $this->name
                ]
            ]
        ];

        if ( ! empty($outgoing))
            $xmlArray[$key]['outgoing'] = $outgoing->getId();

        if ( ! empty($incoming))
            $xmlArray[$key]['incoming'] = $incoming->getId();

        return $xmlArray;
    }

    /**
     * @return TypeElementAbstract
     */
    public function getPrevEl(): ?TypeElementAbstract
    {
        return $this->prevEl;
    }

    /**
     * @param TypeElementAbstract $prevEl
     */
    public function setPrevEl(TypeElementAbstract $prevEl): void
    {
        $this->prevEl = $prevEl;
    }

}
