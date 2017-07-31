<?php
use yii\helpers\Html;
?>
<?php foreach ($realty as $item): ?>
	<? if (!$item->photos)
		$item->photos[0] = "0.png";?>
     <div class="vip-ad-box">
		<img width="180" height="140" src="/upload/overhill/realty/<?=$item->photos[0]->filename?>" />
		<div class="vip-ad-box-overlay" onclick="realty.getById(<?=$item->id?>)"></div>
		<div class="vip-ad-tool">
			<a class="to-map" href="javascript:void(0)" title="Просмотреть на карте" onclick="overhillMap.to(<?=$item->latitude?>, <?=$item->longitude?>)"><i class="fa fa-map-marker"></i></a>
		</div>
		<div class="vip-ad-price">
			<span><?=number_format ($item->price,0,"."," ")?> 
			<?php 
				if ($item->currency == "RUR")
					echo '<i class="fa fa-rub"></i>';
				elseif ($item->currency == "EUR")
					echo '<i class="fa fa-eur"></i>';
				elseif ($item->currency == "USD")
					echo '<i class="fa fa-usd"></i>';
			?> </span>
		</div>
		<div class="vip-ad-info" onclick="realty.getById(<?=$item->id?>)"><?=$item->address?></div>
	</div>
<?endforeach; ?>

