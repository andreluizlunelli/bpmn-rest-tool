<?php

use andreluizlunelli\BpmnRestTool\Controller\IndexController;

$app->get('/', IndexController::class.':index');
$app->get('/bpmn', IndexController::class.':bpmn');
$app->get('/carregar-xml-project', IndexController::class.':carregarXmlProject');
$app->post('/carregar-xml-project', IndexController::class.':postCarregarXmlProject');

