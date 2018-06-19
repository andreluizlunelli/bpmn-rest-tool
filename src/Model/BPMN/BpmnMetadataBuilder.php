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
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementInterface;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

class BpmnMetadataBuilder
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
     * @return TypeElementAbstract
     * @throws ArrayEmptyException
     */
    public function buildMetadata(): TypeElementAbstract
    {
        if (empty($this->project->getTasks()))
            throw new ArrayEmptyException();

        /** @var TypeElementInterface $previousElement */
        $previousElement = $rootEl = null;

        $listTasks = $this->project->getTasks();

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
        $node = null;
        if (empty($previousElement)) // primeiro elemento
            return StartEvent::createFromTask($task);

        else if ($countTasks-1 == $key) // ultimo elemento
            $node = EndEvent::createFromTask($task);

        else if ( ! empty($task->getName()))
            $node = TaskActivity::createFromTask($task);

        if (empty($node))
            throw new \Exception('Não foi possivel criar o elemento');

        $previousElement->addOutgoing($node);
        return $node;
    }

}