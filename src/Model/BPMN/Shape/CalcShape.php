<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 09/04/2019 20:56
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;

class CalcShape
{
    /**
     * @var int
     */
    private $x = 0;

    /**
     * @var int
     */
    private $y = 0;

    private $elStack = [];

    private $elStartEvent = [
        'x' => 36
        ,'y' => 36
    ];

    private $elEndEvent = [
        'x' => 36
        ,'y' => 36
    ];

    /**
     * CalcShape constructor.
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x = 100, int $y = 100)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getxyStartEvent(): P
    {
        $incx = 50;

        $this->x += $incx;

        array_push($this->elStack, StartEvent::class);

        return new P($this->x, $this->y);
    }

    public function getxyEndEvent(): P
    {
        $incx = 50;

        $this->x += $incx;

        array_push($this->elStack, EndEvent::class);

        return new P($this->x, $this->y);
    }

    public function getxySequence(): array
    {
        $incx = 25;

        $px = ['x' => $this->x, 'y' => $this->y];

        $this->x += $incx;

        $py = ['x' => $this->x, 'y' => $this->y];

        array_push($this->elStack, Sequence::class);

        return [
            [$px['x'], $px['y']]
            ,[$py['x'], $py['y']]
        ];
    }

}