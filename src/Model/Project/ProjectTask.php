<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 19:39
 */

namespace andreluizlunelli\BpmnRestTool\Model\Project;

use andreluizlunelli\BpmnRestTool\Model\Traits\AttrIdNameStartFinishDate;

class ProjectTask
{
    use AttrIdNameStartFinishDate;

    public function __construct()
    {
        $this->id = uniqid();
    }

}