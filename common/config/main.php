<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'baseUrl' => '',
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<action>' => 'site/<action>',
            ],
        ],

    ],
    'aliases' => [
        '@uploads' => dirname(dirname(__DIR__)) . '/frontend/uploads/images/',
        '@uploadsWeb' => '/frontend/uploads/images/',
    ],
];
