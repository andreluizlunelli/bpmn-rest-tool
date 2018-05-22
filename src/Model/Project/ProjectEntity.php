<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 19:27
 */
namespace andreluizlunelli\BpmnRestTool\Model\Project;

use DateTime;

class ProjectEntity
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $nameFile;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array <ProjectTask>
     * @see ProjectTask
     */
    private $tasks = [];

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
     * @return ProjectEntity
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameFile(): string
    {
        return $this->nameFile;
    }

    /**
     * @param string $nameFile
     * @return ProjectEntity
     */
    public function setNameFile(string $nameFile): self
    {
        $this->nameFile = $nameFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ProjectEntity
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @param array $tasks
     * @return ProjectEntity
     */
    public function setTasks(array $tasks): self
    {
        $this->tasks = $tasks;
        return $this;
    }

    public function addTask(ProjectTask $projectTask): self
    {
        array_push($this->tasks, $projectTask);
        return $this;
    }

}