<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 21:30
 */

namespace andreluizlunelli\BpmnRestTool\Model\Traits;

use DateTime;

trait AttrIdNameStartFinishDate
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $finishDate;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return AttrIdNameStartFinishDate
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AttrIdNameStartFinishDate
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     * @return AttrIdNameStartFinishDate
     */
    public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    /**
     * @param DateTime $finishDate
     * @return AttrIdNameStartFinishDate
     */
    public function setFinishDate(DateTime $finishDate): self
    {
        $this->finishDate = $finishDate;
        return $this;
    }

}