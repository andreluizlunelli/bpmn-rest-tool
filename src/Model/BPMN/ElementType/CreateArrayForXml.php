<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 21/06/2018
 * Time: 19:52
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

interface CreateArrayForXml
{
    public function createArrayForXml(string $incoming = '', string $outgoing = ''): array;
}