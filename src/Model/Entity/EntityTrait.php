<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/01/2018
 * Time: 22:16
 */

namespace andreluizlunelli\BpmnRestTool\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait EntityTrait
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     *
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

}