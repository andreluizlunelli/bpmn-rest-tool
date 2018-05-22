<?php
return [
    'settings' => [
        'url' => 'http://localhost:8080',
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../templates',
            'template_path_twig' => __DIR__ . '/../templates/twig',
            'twig' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // doctrine
        'doctrine' => [
            'dev_mode' => true
            , 'entity_path' => [
                'src/Model/Entity'
            ],
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'dbname' => 'rodizio',
            'user' => 'postgres',
            'password' => 'postgres'
        ]
    ],
];
