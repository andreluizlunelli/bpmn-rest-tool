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
     * Cria a árvore binária
     *
     * @return TypeElementAbstract
     * @throws ArrayEmptyException
     */
    public function buildMetadata(): TypeElementAbstract
    {
        if (empty($this->project->getTasks()))
            throw new ArrayEmptyException();

        $prevElement = $this->rootEl = null;

        $listTasks = $this->project->getTasks();

        $iteratorTasks = new \ArrayIterator($listTasks);

        while ($iteratorTasks->valid()) {
            $curTask = $iteratorTasks->current();

            if (empty($this->rootEl)) {
                $prevElement = StartEvent::createFromTask(new ProjectTask('', 0));
                $this->rootEl = $prevElement;
            }

            $curEl = $this->createElement($curTask, $prevElement);
            $prevElement = $curEl;
            $iteratorTasks->next();
        }

        $this->addEndEvent();

        return $this->rootEl;
    }

    /**
     * @param ProjectTask $task
     * @param TypeElementAbstract|null $previousElement
     * @return TypeElementAbstract
     * @throws \Exception
     */
    private function createElement(ProjectTask $task, ?TypeElementAbstract $previousElement): TypeElementAbstract
    {
        if ($previousElement instanceof StartEvent) {
            $el = TaskActivity::createFromTask($task);
            $el->setPrevEl($previousElement);
            $previousElement->setOutgoing($el);
            return $el;
        }
        // cria um subProcesso
        // esse if aqui tem que dar == 1, pra não associar com OutlineLevel com mais de um nível. bagunçando então a identação definida no project
        if (($task->getOutlineLevel() - $previousElement->projectTask->getOutlineLevel()) == 1) {
            // significa que o previosElement é um subprocess
            $previousElement = $this->changeTypeTaskActivityToSubProcess($previousElement);
            $startEvent = StartEvent::createFromTask(new ProjectTask('', $task->getOutlineLevel()));
            $startEvent->setPrevEl($previousElement);
            $taskActivity = TaskActivity::createFromTask($task);
            $taskActivity->setPrevEl($startEvent);
            $startEvent->setOutgoing($taskActivity);
            $previousElement->setSubprocess($startEvent);
            return $taskActivity;
        }
        // cria uma tarefa
        if ($task->getOutlineLevel() == $previousElement->projectTask->getOutlineLevel()) {
            // significa que deve criar apenas uma taskActivity pois está no mesmo nível de identação no ms-project
            $taskActivity = TaskActivity::createFromTask($task);
            $taskActivity->setPrevEl($previousElement);
            $previousElement->setOutgoing($taskActivity);
            return $taskActivity;
        }
        // cria uma tarefa como outgoing do ultimo subProcess
        if (($task->getOutlineLevel() - $previousElement->projectTask->getOutlineLevel()) == -1) {
            $outlineLevelSearch = $previousElement->projectTask->getOutlineLevel();
            $prevOutgoing = $this->getPrevOutgoing($outlineLevelSearch);
            $taskActivity = TaskActivity::createFromTask($task);
            $taskActivity->setPrevEl($prevOutgoing);
            $prevOutgoing->setOutgoing($taskActivity);
            return $taskActivity;
        }
        
        if (($task->getOutlineLevel() - $previousElement->projectTask->getOutlineLevel()) < -1) {
            $outlineLevelSearch = $task->getOutlineLevel();
            $prevOutgoing = $this->getElOutLevel($outlineLevelSearch);
            $taskActivity = TaskActivity::createFromTask($task);
            $taskActivity->setPrevEl($prevOutgoing);
            $prevOutgoing->setOutgoing($taskActivity);
            return $taskActivity;
        }

        throw new \Exception('Não foi possivel criar o elemento');
    }

    private function changeTypeTaskActivityToSubProcess2(TypeElementAbstract $changeElement): SubProcess
    {
        $subProcess = $this->createSubProcessFromTaskActivity($changeElement);
        $outlineNumber = $subProcess->projectTask->domQuery->find('OutlineNumber')->text();
        $prevEl = &$this->getParentOutLevel($subProcess);
        $find = $prevEl->getOutgoing();
        while ( ! is_null($find->getOutgoing()))
            $find = &$find->getOutgoing();

        $find = &$subProcess;
        return $subProcess;
    }

    /**
     * @param string $outlineNumber ex: 1.1.1
     * @return TypeElementAbstract
     */
    private function &getElementByOutlineNumber(string $outlineNumber): TypeElementAbstract
    {
        $explode = explode('.', $outlineNumber);
    }

    private function createSubProcessFromTaskActivity(TypeElementAbstract &$changeElement): SubProcess
    {
        $subProcess = new SubProcess($changeElement->projectTask, $changeElement->getOutgoing());
        $subProcess->setId($changeElement->getId());
        $subProcess->setPrevEl($changeElement->getPrevEl());
        return $subProcess;
    }

    private function changeTypeTaskActivityToSubProcess(TaskActivity $changeElement): SubProcess
    {
        $prev = $this->rootEl;
        $outgoing = $prev->getOutgoing();
        $find = false;
        $subProcess = null;
        do {
            if ($outgoing->projectTask->getId() == $changeElement->projectTask->getId()) {
                $find = true;
                $subProcess = new SubProcess($changeElement->projectTask, $outgoing->getOutgoing());
                if ($prev instanceof SubProcess) {
                    if ($changeElement->projectTask->domQuery->find('OutlineLevel')->text()
                    == $prev->projectTask->domQuery->find('OutlineLevel')->text())
                        $prev->setOutgoing($subProcess);
                    else
                        $prev->setSubprocess($subProcess);
                }
                else
                    $prev->setOutgoing($subProcess);
            } else {
                $prev = $outgoing;
                $outgoing = $outgoing instanceof SubProcess
                    ? $outgoing->getSubprocess()
                    : $outgoing->getOutgoing();
                if (empty($outgoing)) {
                    $prev = $this->getPrevOutgoing((int)$prev->projectTask->domQuery->find('OutlineLevel')->text());
                    $outgoing = $prev->getOutgoing();
                }
            }
        } while (! $find);
        return $subProcess;
    }

    private function getPrevOutgoing(int $outlineLevelSearch): TypeElementAbstract
    {
        $find = false;
        $prevOutgoing = null;
        $prev = $this->rootEl;
        $current = $prev->getOutgoing();
        do {
            if (( ! $current instanceof StartEvent) && $current->projectTask->getOutlineLevel() == $outlineLevelSearch) {
                $find = true;
                $prevOutgoing = $prev;
            } else {
                if (! $current instanceof StartEvent)
                    $prev = $current;
                $current = $current instanceof SubProcess
                    ? $current->getSubprocess()
                    : $current->getOutgoing();
            }
        } while (! $find);
        return $prevOutgoing;
    }

    private function getElOutLevel(int $outlineLevelSearch): TypeElementAbstract
    {
        $levelEl = $this->rootEl;
        do {
            if ( ! $levelEl)
                throw new \Exception('não achou o prev outlevel');

            if ($levelEl->projectTask->getOutlineLevel() != $outlineLevelSearch) {
                if ( ! $levelEl instanceof SubProcess)
                    $levelEl = $levelEl->getOutgoing();
                else
                    $levelEl = $levelEl->getSubprocess();
                continue;
            }

            while ($levelEl->getOutgoing() != null)
                $levelEl = $levelEl->getOutgoing();
            return $levelEl;

        } while (true);
    }

    private function getPrevOutLevel(int $outlineLevelSearch): TypeElementAbstract
    {
        $levelEl = $this->rootEl;
        do {
            if ( ! $levelEl)
                throw new \Exception('não achou o prev outlevel');

            if ($levelEl->projectTask->getOutlineLevel() != $outlineLevelSearch) {
                if ( ! $levelEl instanceof SubProcess)
                    $levelEl = $levelEl->getOutgoing();
                else
                    $levelEl = $levelEl->getSubprocess();
                continue;
            }

            $prevEl = null;
            while ($levelEl->getOutgoing() != null) {
                $prevEl = $levelEl;
                $levelEl = $levelEl->getOutgoing();
            }
            return $prevEl;

        } while (true);
    }

    private function addEndEvent(): void
    {
        $prev = null;
        $cur = $this->rootEl;
        do {
            if ($cur instanceof SubProcess
            || $cur instanceof TaskActivity) {
                if (empty($cur->getOutgoing())) {
                    $cur->setOutgoing(new EndEvent(new ProjectTask('', 0)));
                } else {
                    if ($cur instanceof SubProcess)
                        $prev = $cur->getOutgoing();
                }
                $cur = $cur instanceof SubProcess ? $cur->getSubprocess() : $cur->getOutgoing();
            } else {
                if ($cur instanceof EndEvent) {
                    $cur = $prev;
                    $prev = null;
                } else
                    $cur = $cur->getOutgoing();
            }

        } while ( ! empty($cur));
    }

    /**
     * @param TypeElementAbstract $taskOrSubprocess pode ser TaskActivity ou SubProcess
     * @return StartEvent
     */
    private function &getParentOutLevel(TypeElementAbstract &$taskOrSubprocess): StartEvent
    {
        $find = $taskOrSubprocess->getPrevEl();
        try {
            while ( ! $find instanceof StartEvent)
                $find = $find->getPrevEl();
        } catch (\Throwable $t) {
        }
        return $find;
    }

}
