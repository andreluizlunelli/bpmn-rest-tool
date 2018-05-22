<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 21/08/2017
 * Time: 20:32
 */

namespace andreluizlunelli\BpmnRestTool\System;

class App
{
    /**
     * @var \Slim\App
     */
    private static $app;

    /**
     * @return \Slim\App|void
     */
    public static function getApp(): \Slim\App
    {
        if (empty(self::$app)) {
            self::$app = self::createApp();
        }
        return self::$app;
    }

    private static function createApp(): \Slim\App
    {
        $settings = require __DIR__ . '/../settings.php';

        self::$app = $app = new \Slim\App($settings);

        // Set up dependencies
        require __DIR__ . '/../dependencies.php';

        // Register middleware
        require __DIR__ . '/../middleware.php';

        // Register routes
        require __DIR__ . '/../routes.php';

        return $app;
    }
}