<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 09/04/2019 20:56
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TaskActivity;
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

    public static $elStartEvent = [
        'w' => 36
        ,'h' => 36
    ];

    public static $elEndEvent = [
        'w' => 36
        ,'h' => 36
    ];

    public static $elTask = [
        'w' => 100
        ,'h' => 100
    ];

    public static $elSequence = [
        'w' => 1
        ,'h' => 0
    ];

    /**
     * CalcShape constructor.
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x = 300, int $y = 25)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getxyStartEvent(): P
    {
        $p = new P($this->x, $this->y);
        $this->y += self::$elStartEvent['h'];
        array_push($this->elStack, StartEvent::class);

        return $p;
    }

    public function getxyEndEvent(): P
    {
        $p = new P($this->x, $this->y);
        $this->y += self::$elEndEvent['h'];
        array_push($this->elStack, EndEvent::class);

        return $p;
    }

    public function getxySequence(): array
    {
        $px = ['x' => $this->x, 'y' => $this->y];

        $this->y += self::$elSequence['h'];

        $py = ['x' => $this->x, 'y' => $this->y];

        array_push($this->elStack, Sequence::class);

        return [
            [$px['x'], $py['x']]
            ,[$px['y'], $py['y']]
        ];
    }

    public function getxyTask(): P
    {
        $p = new P($this->x, $this->y);
        $this->y += self::$elTask['h'];
        array_push($this->elStack, TaskActivity::class);
        return $p;
    }

}
