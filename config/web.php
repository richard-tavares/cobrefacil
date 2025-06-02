<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'MFCDbjPWsBbkyz1qLDPJwMMHfV7zZyj3',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
            'enableAutoLogin' => false,
            'loginUrl' => null,
        ],
        'errorHandler' => [
            'class' => 'yii\web\ErrorHandler',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'api/index',
                'POST register' => 'user/register',
                'POST login'    => 'user/login',
                'POST expense' => 'expense/create',
                'GET expense' => 'expense/index',
                'GET expense/<id:\d+>' => 'expense/view',
                'PUT,PATCH expense/<id:\d+>' => 'expense/update',
                'DELETE expense/<id:\d+>' => 'expense/delete',
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            \app\services\UserService::class => \app\services\UserService::class,
            \app\services\ExpenseService::class => \app\services\ExpenseService::class,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
}

return $config;
