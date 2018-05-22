<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 19:47
 */

namespace andreluizlunelli\BpmnRestTool\Model\Project;

use SplFileObject;
use DateTime;

class ProjectMapper
{
    const DATE_FORMAT_READ = "Y-m-d\TH:i:s";

    public function map(SplFileObject $file): ?ProjectEntity
    {
        $query = qp(file_get_contents($file->getPathname()));

        $title = $query->find('Title')->eq(0)->text() ?? '';
        $nameFile = $query->find('Name')->eq(0)->text() ?? '';

        $project = (new ProjectEntity())
            ->setTitle($title)
            ->setNameFile($nameFile)
        ;

        if ($query->find('Task')->count() < 1)
            return $project;

        foreach ($query->find('Task') as $task)
            $project->addTask((new ProjectTask())
                ->setName($task->find('Name')->text())
                ->setStartDate(
                    DateTime::createFromFormat(self::DATE_FORMAT_READ, $task->find('Start')->text())
                )
                ->setFinishDate(
                    DateTime::createFromFormat(self::DATE_FORMAT_READ, $task->find('Finish')->text())
                )
            );

        return $project;
    }

}