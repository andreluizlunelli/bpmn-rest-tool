<?php

use andreluizlunelli\BpmnRestTool\Controller\IndexController as ic;
use andreluizlunelli\BpmnRestTool\Controller\LoginController as lc;
use andreluizlunelli\BpmnRestTool\Middleware\AuthorizationMiddleware;

$app->any('/login', lc::class.':login')->setName('login');
$app->any('/logout', lc::class.':logout')->setName('logout');

$app->group('/', function () use ($app) {
    $app->get('', ic::class.':index')->setName('index');
    $app->get('bpmn', ic::class.':bpmn');
    $app->get('carregar-xml-project', ic::class.':carregarXmlProject');
    $app->post('carregar-xml-project', ic::class.':postCarregarXmlProject');
})
    ->add(AuthorizationMiddleware::class);

