<?php
return [
    'settings' => [
        'base_assets' => '', // http://localhost:8081, quando usar o comando: webpack start
        'date_formats' => [
            'date_time_format' => 'Y-m-d H:i:s'
        ],
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
            'host' => 'database',
            'dbname' => 'bpmnresttool',
            'user' => 'postgres',
            'password' => 'postgres'
        ]
    ],
];
