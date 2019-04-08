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

        $this->addEndEvent2($this->rootEl);

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
            $previousElement = $this->changeTypeTaskToSubProcess($previousElement);
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
            $taskEl = TaskActivity::createFromTask($task);
            return $this->addTaskToOutgoingSubprocess($taskEl);
        }

        throw new \Exception('Não foi possivel criar o elemento');
    }

    private function addTaskToOutgoingSubprocess(TaskActivity $task): TaskActivity
    {
        $prev = null;
        $find = $this->rootEl;
        $outlineNumber = $task->projectTask->domQuery->find('OutlineNumber')->text();
        $outLineLevel = $task->projectTask->getOutlineLevel();
        $explodeOutline = explode('.', $outlineNumber);

        for ($i=0; $i<$outLineLevel; $i++) {
            if ($find instanceof StartEvent)
                $find = $find->getOutgoing();
            if ($find instanceof SubProcess)
                $find = $find->getSubprocess();

            for ($j=0; $j<(int)$explodeOutline[0]; $j++) {
                $prev = $find;
                $find = $find->getOutgoing();
            }

        }

        $task->setPrevEl($prev);
        $prev->setOutgoing($task);
        return $task;
    }

    private function changeTypeTaskToSubProcess(TaskActivity $changeElement): SubProcess
    {
        $find = $this->rootEl;
        $outlineNumber = $changeElement->projectTask->domQuery->find('OutlineNumber')->text();
        $outLineLevel = $changeElement->projectTask->getOutlineLevel();
        $explodeOutline = explode('.', $outlineNumber);

        if ($explodeOutline[0] != '0') {
            foreach ($explodeOutline as $n) {
                if ($find instanceof StartEvent)
                    $find = $find->getOutgoing();
                if ($find instanceof SubProcess)
                    $find = $find->getSubprocess();
                for ($i=0; $i<(int)$n; $i++) {
                    $prev = $find;
                    $find = $find->getOutgoing();
                }
            }
        }

        if (isset($prev)) {
            $subProcess = $this->createSubProcessFromTaskActivity($changeElement);
            $prev->setOutgoing($subProcess);
        } else {
            $subProcess = $this->createSubProcessFromTaskActivity($changeElement);
            $find->setOutgoing($subProcess);
        }
        return $subProcess;
    }

    private function createSubProcessFromTaskActivity(TypeElementAbstract &$changeElement): SubProcess
    {
        $subProcess = new SubProcess($changeElement->projectTask, $changeElement->getOutgoing());
        $subProcess->setId($changeElement->getId());
        $subProcess->setPrevEl($changeElement->getPrevEl());
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

    private function addEndEvent2(TypeElementAbstract &$cur): void
    {
        $tmpCur = $cur;
        if ($tmpCur instanceof StartEvent)
            $tmpCur = $tmpCur->getOutgoing();
        if ($tmpCur instanceof TaskActivity)
            if ($tmpCur->getOutgoing() == null)
                $tmpCur->setOutgoing(new EndEvent(new ProjectTask('', $tmpCur->projectTask->getOutlineLevel())));
            else{
                $tmpTask = $tmpCur->getOutgoing();
                $this->addEndEvent2($tmpTask);
            }
        if ($tmpCur instanceof SubProcess) {
            if ($tmpCur->getOutgoing() == null)
                $tmpCur->setOutgoing(new EndEvent(new ProjectTask('', $tmpCur->projectTask->getOutlineLevel())));

            $tmpStart = $tmpCur->getSubprocess();
            $this->addEndEvent2($tmpStart);

            $tmpSub = $tmpCur->getOutgoing();
            $this->addEndEvent2($tmpSub);
        }
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

    /**
     * @param TypeElementAbstract $taskOrSubprocess pode ser TaskActivity ou SubProcess
     * @return StartEvent
     */
    private function getParentOutLevel(TypeElementAbstract $taskOrSubprocess): StartEvent
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
