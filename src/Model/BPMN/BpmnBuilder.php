<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 20:24
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Exception\ArrayEmptyException;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementInterface;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

class BpmnBuilder
{
    /**
     * @var ProjectEntity
     */
    private $project;

    /**
     * BpmnMapper constructor.
     * @param ProjectEntity $project
     */
    public function __construct(ProjectEntity $project)
    {
        $this->project = $project;
    }

    /**
     * @throws ArrayEmptyException
     */
    public function buildMetadata()
    {
        if (empty($this->project->getTasks()))
            throw new ArrayEmptyException();

        $listElement = [];
        $listTasks = $this->project->getTasks();

        /** @var TypeElementInterface $previousElement */
        $previousElement = null;

        array_walk($listTasks, function ($item, $k) use ($listElement, $previousElement) {
            $actualElement = $this->createElement($listElement, $item, $k, $previousElement);

            array_push($listElement, $actualElement);

            $previousElement = $actualElement;
        });

        return $listElement;
    }

    /**
     * @param array $listElement
     * @param ProjectTask $task
     * @param $key
     * @param TypeElementAbstract|null $previousElement
     * @return TypeElementInterface
     * @throws \Exception
     */
    public function createElement(array $listElement, ProjectTask $task, $key, ?TypeElementAbstract $previousElement): TypeElementInterface
    {
        if (empty($previousElement)) // primeiro elemento
            return StartEvent::createFromTask($task);

        if (count($listElement)-1 == $key) // ultimo elemento
            return $previousElement->addOutgoing(
                EndEvent::createFromTask($task)
            );

        throw new \Exception('Não foi possivel criar o elemento');
    }

}