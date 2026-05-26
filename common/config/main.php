<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'components' => [
        'db' => [
            'class'   => 'yii\db\Connection',
            'charset' => 'utf8mb4',
            // Override dsn/username/password in main-local.php
        ],

        'mailer' => [
            'class'            => 'yii\symfonymailer\Mailer',
            'viewPath'         => '@common/mail',
            // In dev, write emails to file instead of sending
            'useFileTransport' => true,
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
