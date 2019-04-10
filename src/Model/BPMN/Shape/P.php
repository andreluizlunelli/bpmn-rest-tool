<?php
/**
 * User: andreluizlunelli
 * E-mail: to.lunelli@gmail.com
 * Date: 09/04/2019 21:04
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Shape;

class P
{
    private $x;
    private $y;

    /**
     * P constructor.
     * @param $x
     * @param $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return mixed
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return mixed
     */
    public function getY() : int
    {
        return $this->y;
    }

}