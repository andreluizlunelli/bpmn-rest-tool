<?php
// ROTAS API
$app->group('/api', function () use ($app) {
    // TOKEN
    $app->post('/token', \andreluizlunelli\BpmnRestTool\Controller\TokenController::class . ':post');
});

// ADMIN
$app->group('/admin', function () use ($app) {
    $app->get('', \andreluizlunelli\BpmnRestTool\Controller\Admin\SugestaoController::class . ':sugestoes');
})->add(new \andreluizlunelli\BpmnRestTool\Middleware\AutorizacaoMiddleware());

// TELAS SITE
$app->get('/', function ($request, $response, $args) {
    echo ':)';die;
})->setName('home');

