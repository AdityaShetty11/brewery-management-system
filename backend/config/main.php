<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/../../common/config/main-local.php';

$config = [
    'id'          => 'app-backend',
    'basePath'    => dirname(__DIR__),
    'bootstrap'   => ['log'],
    'controllerNamespace' => 'backend\controllers',
    'language'    => 'en-US',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'modules' => [
        'crm'        => ['class' => 'backend\modules\crm\CrmModule'],
        'product'    => ['class' => 'backend\modules\product\ProductModule'],
        'order'      => ['class' => 'backend\modules\order\OrderModule'],
        'production' => ['class' => 'backend\modules\production\ProductionModule'],
        'inventory'  => ['class' => 'backend\modules\inventory\InventoryModule'],
        'report'     => ['class' => 'backend\modules\report\ReportModule'],
    ],

    'components' => [
        'request' => [
            'csrfParam'           => '_csrf-backend',
            'baseUrl'             => '',
            'cookieValidationKey' => 'hops-barrel-backend-secret-k3y-2024',
        ],
        'assetManager' => [
            'bundles' => [
                // Bootstrap CSS/JS comes from CDN in the layout — disable vendor copies
                'yii\bootstrap5\BootstrapAsset'       => ['css' => []],
                'yii\bootstrap5\BootstrapPluginAsset' => ['js'  => [], 'depends' => [\yii\web\JqueryAsset::class]],
            ],
        ],
        'user' => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl'        => ['/site/login'],
        ],
        'session' => [
            'name' => 'advanced-backend',
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
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [],
        ],
    ],

    'params' => $params,
];

return \yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    $db,
    $config
);
