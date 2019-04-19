<?php
// DIC configuration

use andreluizlunelli\BpmnRestTool\Model\Twig\ViewFunctions;
use andreluizlunelli\BpmnRestTool\System\Database;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionArrayStorage;
use Slim\Flash\Messages;

$container = $app->getContainer();

// twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path_twig'], $settings['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new ViewFunctions($c));
    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['em'] = Database::getEntityManager();

$container['SessionManager'] = function ($c) {
    $sessionManager = new SessionManager(new SessionConfig(), new SessionArrayStorage());
    $sessionManager->start();
    return $sessionManager;
};

$container['message'] = function () {
    return new Messages();
};
