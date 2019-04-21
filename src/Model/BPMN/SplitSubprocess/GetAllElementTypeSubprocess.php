<?php

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;

class GetAllElementTypeSubprocess
{
    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

    /**
     * @var array
     */
    private $listSubprocess;

    /**
     * GetAllElementTypeSubprocess constructor.
     * @param TypeElementAbstract $rootEl
     */
    public function __construct(TypeElementAbstract $rootEl)
    {
        $this->rootEl = $rootEl;
        $this->listSubprocess = [];
    }

    /**
     * @return array lista de SubProcess
     *
     * @see SubProcess
     */
    public function all(): array
    {
        $el = $this->rootEl;
        $this->allRecursive($el);
        return $this->listSubprocess;
    }

    private function allRecursive(?TypeElementAbstract $el): void
    {
        if ($el === null)
            return;

        if ($el instanceof SubProcess) {
            array_push($this->listSubprocess, $el);
            $this->allRecursive($el->getSubprocess());
            $this->allRecursive($el->getOutgoing());
            return;
        }

        $el = $el->getOutgoing();
        $this->allRecursive($el);
    }

}