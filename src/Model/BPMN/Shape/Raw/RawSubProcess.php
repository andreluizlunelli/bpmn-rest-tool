<?php
/**
 * Criado por: andre.lunelli
 * Date: 28/03/2019 - 09:25
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape\Raw;

use andreluizlunelli\BpmnRestTool\Model\Traits\AttrElement;

class RawSubProcess
{
    use AttrElement;

    /**
     * @var RawStart
     */
    public $start;

    /**
     * @var RawEnd
     */
    public $end;

    /**
     * @var array
     */
    public $listSubProcess;

    /**
     * @var array
     */
    public $listTask;

    /**
     * RawSubProcess constructor.
     *
     * Quando é enviado um SubProcess, não pode ser enviado um Task
     *
     * @param string $id
     * @param string $name
     * @param RawStart $start
     * @param RawEnd $end
     * @param array $listSubProcess
     * @param array $listTask
     * @throws \Exception
     */
    public function __construct(string $id, string $name, RawStart $start, RawEnd $end, ?array $listSubProcess, ?array $listTask)
    {
        $this->id = $id;
        $this->name = $name;
        $this->start = $start;
        $this->end = $end;
        if ( ! (! empty($listSubProcess) xor ! empty($listTask)))
            throw new \Exception('Não pode enviar listSubProcess e listTask');

        $this->listSubProcess = $listSubProcess;
        $this->listTask = $listTask;
    }


}
