<?php

$params = require __DIR__ . '/../../common/config/main.php';
$db     = require __DIR__ . '/../../common/config/main-local.php';

$config = [
    'id'          => 'app-console',
    'basePath'    => dirname(__DIR__),
    'bootstrap'   => ['log'],
    'controllerNamespace' => 'console\controllers',

    'components' => [
        'log' => [
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],

    'params' => [],
];

return \yii\helpers\ArrayHelper::merge($params, $db, $config);
