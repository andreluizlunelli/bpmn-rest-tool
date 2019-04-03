<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 19:47
 */

namespace andreluizlunelli\BpmnRestTool\Model\Project;

use SplFileObject;

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

        $project->domQuery = $query;

        if ($query->find('Task')->count() < 1)
            return $project;

        foreach ($query->find('Task') as $taskQuery) {
            $pt = new ProjectTask(
                $taskQuery->find('Name')->text()
                , (int)$taskQuery->find('OutlineLevel')->text()
            );
            $pt->domQuery = clone $taskQuery;
            $project->addTask($pt);
        }
        return $project;
    }

}
