<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 19:39
 */

namespace andreluizlunelli\BpmnRestTool\Model\Project;

use DateTime;

class ProjectTask
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $finishDate;

    public function __construct()
    {
        $this->id = uniqid();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return ProjectTask
     */
    public function setId(string $id): ProjectTask
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
     * @return ProjectTask
     */
    public function setName(string $name): ProjectTask
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
     * @return ProjectTask
     */
    public function setStartDate(DateTime $startDate): ProjectTask
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
     * @return ProjectTask
     */
    public function setFinishDate(DateTime $finishDate): ProjectTask
    {
        $this->finishDate = $finishDate;
        return $this;
    }

}