<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/08/2017
 * Time: 18:59
 */

namespace andreluizlunelli\BpmnRestTool\Model\Entity;

interface ToExpose
{
    public function toArray(): array;
    public function toExpose(): string;
}