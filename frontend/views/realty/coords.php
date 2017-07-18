<?php
use yii\helpers\Html;
?>
{"obj": [
<?php $delimeter = "";?>
<?php foreach ($realty as $item) :?>
<?=$delimeter?> {"lat": <?=$item->latitude?>, "lng": <?=$item->longitude?>}
<?php $delimeter = ",";?>
<?php endforeach; ?>
]}