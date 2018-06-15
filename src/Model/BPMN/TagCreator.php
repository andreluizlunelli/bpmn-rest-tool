<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 19:52
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN;

interface TagCreator
{
    public function create(): string;
}