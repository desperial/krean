<?php
use yii\helpers\Html;
?>
<? foreach ($countries as $item) : ?>
<div class="country">
	<img src="/upload/overhill/icons/countries/<?=strtolower($item['code']);?>.png" align="middle" />
	<a href="javascript:realty.chooseCountry('<?=$item['latitude']?>','<?=$item['longitude']?>', '<?=$item['code']?>')"><?=$item['name']?></a> 
	<sup><?=$item['cnt']?></sup>
</div>
<? endforeach; ?>