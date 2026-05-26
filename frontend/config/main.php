<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/../../common/config/main-local.php';

$config = [
    'id'          => 'app-frontend',
    'basePath'    => dirname(__DIR__),
    'bootstrap'   => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'language'    => 'en-US',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'components' => [
        'request' => [
            'csrfParam'           => '_csrf-frontend',
            'baseUrl'             => '',
            'cookieValidationKey' => 'hops-barrel-frontend-secret-k3y-2024',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset'       => ['css' => []],
                'yii\bootstrap5\BootstrapPluginAsset' => ['js'  => [], 'depends' => [\yii\web\JqueryAsset::class]],
            ],
        ],
        'user' => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'rules'               => [
                ''                                    => 'site/index',
                'login'                               => 'site/login',
                'logout'                              => 'site/logout',
                'signup'                              => 'site/signup',
                'verify-email/<token>'                => 'site/verify-email',
                'request-password-reset'              => 'site/request-password-reset',
                'reset-password/<token>'              => 'site/reset-password',
            ],
        ],
    ],

    'params' => $params,
];

return \yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    $db,
    $config
);
