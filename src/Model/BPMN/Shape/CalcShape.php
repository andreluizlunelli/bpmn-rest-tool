<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 09/04/2019 20:56
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\EndEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\StartEvent;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\SubProcess;
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

    /**
     * @var array utilizado para debug
     */
    private static $elStack = [];

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

    public static $elSubprocess = [
        'w' => 150
        ,'h' => 1
    ];

    public static $elSequence = [
        'w' => 1
        ,'h' => 25
        ,'recuoCentro' => 18
    ];

    public static $sumWSubprocess = 0;
    public static $incWSubprocess = 50;

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

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $x
     */
    public function setX(int $x): void
    {
        $this->x = $x;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }

    public function getxyStartEvent(): P
    {
        $p = new P($this->x, $this->y);
        $this->y += self::$elStartEvent['h'];
        array_push(self::$elStack, StartEvent::class . "     y:" . $this->y . ' x:' . $this->x);

        return $p;
    }

    public function getxyEndEvent(): P
    {
        $p = new P($this->x, $this->y);
        $this->y += self::$elEndEvent['h'] + 10;
        array_push(self::$elStack, EndEvent::class . "     y:" . $this->y . ' x:' . $this->x);

        return $p;
    }

    public function getxySequence(): array
    {
        $px = ['x' => $this->x + self::$elSequence['recuoCentro'], 'y' => $this->y];

        $this->y += self::$elSequence['h'];

        $py = ['x' => $this->x + self::$elSequence['recuoCentro'], 'y' => $this->y];

        array_push(self::$elStack, Sequence::class . "     y:" . $this->y . ' x:' . $this->x);

        return [
            [$px['x'], $py['x']]
            ,[$px['y'], $py['y']]
        ];
    }

    public function getxyTask(): P
    {
        $p = new P($this->x - (self::$elTask['w']/2) + self::$elSequence['recuoCentro'], $this->y);
        $this->y += self::$elTask['h'];
        array_push(self::$elStack, TaskActivity::class . "     y:" . $this->y . ' x:' . $this->x);
        return $p;
    }

    public function getxySubprocess(): P
    {
        $p = new P($this->x - (self::$elSubprocess['w']/2) + self::$elSequence['recuoCentro'], $this->y);
        $this->y += self::$elSubprocess['h'];

        array_push(self::$elStack, SubProcess::class . "     y:" . $this->y . ' x:' . $this->x);
        return $p;
    }

    public static function clearSumWidth()
    {
        self::$sumWSubprocess = 0;
        self::$incWSubprocess = 50;
    }
}
