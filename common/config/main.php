<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                '<action>' => 'site/<action>',
                '<controller>/index' => '<controller>',
            ],
        ],

    ],
    'aliases' => [
        '@uploads' => dirname(dirname(__DIR__)) . '/frontend/uploads/images/',
        '@uploadsWeb' => '/frontend/uploads/images/',
    ],
];
