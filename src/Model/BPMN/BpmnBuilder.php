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

        $rootEl = null;
        $listTasks = $this->project->getTasks();

        /** @var TypeElementInterface $previousElement */
        $previousElement = null;

        $countTasks = count($listTasks);

        array_walk($listTasks, function ($item, $k) use (&$rootEl, &$previousElement, $countTasks) {
            $actualElement = $this->createElement($countTasks, $item, $k, $previousElement);

            if (empty($rootEl))
                $rootEl = $actualElement;

            $previousElement = $actualElement;
        });

        return $rootEl;
    }

    /**
     * @param int $countTasks
     * @param ProjectTask $task
     * @param $key
     * @param TypeElementAbstract|null $previousElement
     * @return TypeElementInterface
     * @throws \Exception
     */
    private function createElement(int $countTasks, ProjectTask $task, $key, ?TypeElementAbstract $previousElement): TypeElementInterface
    {
        if (empty($previousElement)) // primeiro elemento
            return StartEvent::createFromTask($task);

        if ($countTasks-1 == $key) // ultimo elemento
            return $previousElement->addOutgoing(
                EndEvent::createFromTask($task)
            );

        throw new \Exception('Não foi possivel criar o elemento');
    }

}