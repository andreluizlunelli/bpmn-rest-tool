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
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementInterface;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectTask;

/**
 * Class BpmnMetadataBuilder
 * @package andreluizlunelli\BpmnRestTool\Model\BPMN
 *
 * Cria o metadado em array php utilizado pelo criador de BPMN
 */
class BpmnMetadataBuilder
{
    /**
     * @var ProjectEntity
     */
    private $project;

    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

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
        $previousElement = $this->rootEl = null;

        $listTasks = $this->project->getTasks();

        $countTasks = count($listTasks);

        array_walk($listTasks, function ($item, $k) use (&$previousElement, $countTasks) {
            /** @var TypeElementAbstract $actualElement */
            $actualElement = $this->createElement($countTasks, $item, $k, $previousElement);

            if (empty($this->rootEl)) {
                $this->rootEl = $actualElement;
                $previousElement = current($actualElement->getOutgoing());
            } else {
                $previousElement = $actualElement;
            }

        });

        return $this->rootEl;
    }

    /**
     * @param int $countTasks
     * @param ProjectTask $task
     * @param $key
     * @param TypeElementAbstract|null $previousElement
     * @return TypeElementInterface
     * @throws \Exception
     */
    private function createElement(int $countTasks, ProjectTask $task, $key, ?TypeElementAbstract &$previousElement): TypeElementInterface
    {
        if ($key === 0) {
            $startEvent = StartEvent::createFromTask(new ProjectTask());
            $taskActivity = TaskActivity::createFromTask($task);
            $startEvent->addOutgoing($taskActivity);
            return $startEvent;
        }

        if ((int)$task->domQuery->find('OutlineLevel')->text() > (int)$previousElement->projectTask->domQuery->find('OutlineLevel')->text()) {
            // significa que o previosElement é um subprocess
            $previousElement = $this->changeTypeTaskActivityToSubProcess($previousElement);
            $taskActivity = TaskActivity::createFromTask($task);
            $previousElement->setOutgoing($taskActivity);
            return $taskActivity;
        }


        $node = null;
        if (empty($previousElement)) // primeiro elemento
            return StartEvent::createFromTask($task);

        else if ($countTasks-1 == $key) // ultimo elemento
            $node = EndEvent::createFromTask($task);

        else if ( ! empty($task->getName()))
            $node = TaskActivity::createFromTask($task);

        if (empty($node))
            throw new \Exception('Não foi possivel criar o elemento');

        $previousElement->setOutgoing($node);
        return $node;
    }

    private function changeTypeTaskActivityToSubProcess(TypeElementAbstract $previousElement): TypeElementAbstract
    {
        $previousSubprocess = SubProcess::createFromTask($previousElement->projectTask);
        do {
            $outgoing = $this->rootEl->getOutgoing();

            if ($outgoing->projectTask->getId() == $previousElement->projectTask->getId()) {
                
            }

        } while (! empty($outgoing));

    }

}
