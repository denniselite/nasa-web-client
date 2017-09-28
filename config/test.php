<?php
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/test_db.php');

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),    
    'language' => 'en-US',
    'components' => [
        'mongodb' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'api' => [
            'class' => 'app\components\Api',
            'endPoint' => 'https://api.nasa.gov/neo/rest/v1',
            'apiKey' => 'N7LkblDsc5aen05FJqBQ8wU4qSdmsftwJagVK7UD'
        ],
        'assetManager' => [            
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'neo'],
                '/' => 'app/index',
                '<controller>/<action>' => '<controller>/<action>'
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Egs-Kt-yqvJ9Go_trob1AsZgrhtca8-P',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'app/error',
        ],
    ],
    'params' => $params,
];
