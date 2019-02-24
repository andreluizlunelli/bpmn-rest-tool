<?php

use andreluizlunelli\BpmnRestTool\Controller\IndexController as ic;
use andreluizlunelli\BpmnRestTool\Controller\LoginController as lc;

$app->get('/login', lc::class.':login');
$app->get('/', ic::class.':index');
$app->get('/bpmn', ic::class.':bpmn');
$app->get('/carregar-xml-project', ic::class.':carregarXmlProject');
$app->post('/carregar-xml-project', ic::class.':postCarregarXmlProject');

