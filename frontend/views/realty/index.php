<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<h1>Realty</h1>
<ul>
<?php foreach ($realty as $item): ?>
    <li>
        <?= Html::encode("{$item->google_address} ({$item->group})") ?>:
        <?= $item->view ?>
    </li>
<?php endforeach; ?>
</ul>

<?= LinkPager::widget(['pagination' => $pagination]) ?>