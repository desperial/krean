<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=0.5, minimum-scale=0.5, scale=0.5">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<div id="page-loader" style="display: none;">
    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
</div>
<?php $this->beginBody() ?>
    <!--<div class="overhill-header">
        <div class="overhill-header-left">
            <div class="logo">
                <a href="javascript:void(0)" onclick="overhill.reload()"></a>
            </div>
            <div class="menu">
                <ul>
                    <li>
                        <span><i class="fa fa-bars"></i></span>
                        <ul>
                            <li><a href="javascript:void(0)" onclick="overhillPage.open(4)">Платные услуги</a></li>
                            <li><a href="javascript:void(0)" onclick="overhillPage.open(3)">О нас</a></li>
                            <li><a href="javascript:void(0)" onclick="overhillPage.open(2)">Контакты</a></li>
                            <li><a href="javascript:void(0)" onclick="overhillPage.open(1)">Условия пользования</a></li>
                            <li><a href="javascript:void(0)" onclick="overhill.switchFullscreen(this)">Полноэкранный режим</a></li>
                            <li><a href="javascript:void(0)" onclick="overhill.switchMap(this)">Отключить карту (beta)</a></li>
                        </ul>
                    </li>
                    <li>
                        <span>Валюта</span>
                        <ul>
                            <li><a href="javascript:void(0)" onclick="overhill.user.setCurrency('RUB')">RUR (Российский рубль)</a></li>
                            <li><a href="javascript:void(0)" onclick="overhill.user.setCurrency('USD')">USD (Американский доллар)</a></li>
                            <li><a href="javascript:void(0)" onclick="overhill.user.setCurrency('EUR')">EUR (Евро)</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="/articles/"><span>Статьи</span></a>
                    </li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
        <div class="overhill-header-right">
            <div class="menu">
                <ul>
                    <li style="display: none"><a href="javascript:void(0)" onclick="overhillRealty.service.callback(this)">Обратный звонок</a></li>
                    <li><a href="javascript:void(0)" onclick="overhill.callBackHunterInit()"><img calss="callback" src="/imgs/phone-512-1.png" width="50" height="50" style="margin-top:0px;"></a></li>
                </ul>
            </div>
            <div class="phone">
                <span>+7 (346) 2XX-XX-XX</span>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>-->
    <?= $content ?>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Depala.ru <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
