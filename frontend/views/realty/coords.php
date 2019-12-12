<?php
use yii\helpers\Html;
?>
{"obj": [
<?php $delimeter = "";?>
<?php foreach ($realty as $item) :?>
<? if ($item->lat != null && $item->lon != null) :?>
<?=$delimeter?> {"lat": <?=$item->lat?>, "lng": <?=$item->lon?>, "id": <?=$item->id?>}
<?php $delimeter = ",";?>
<? endif;?>
<?php endforeach; ?>
]}