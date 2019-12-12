<?php
use yii\bootstrap\Tabs;

echo Tabs::widget([
    'items' => [
        [
            'label'     => 'Местоположение объекта',
            'content'   => $this->render('auth'),
            'active'    => true
        ],
        [
            'label'     => 'Основные параметры объекта',
            'content'   => $this->render('register')
        ],
        [
            'label'     => 'Описание объекта',
            'content'   => $this->render('restore')
        ],
        [
            'label'     => 'Фотографии объекта',
            'content'   => $this->render('restore')
        ]
    ]
]);
?>