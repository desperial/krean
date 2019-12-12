<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
?>
<?php Pjax::begin(['enablePushState' => false, 'linkSelector' => '.realtyPage', 'scrollTo' => true]);  ?>
<?php foreach ($realty as $item): ?>
        <div class="item ad-from-list-<?=$item->id?>" location:latitude="<?=$item->lat?>" location:longitude="<?=$item->lon?>">
				<div class="item-group-1">
					<div class="item-data-photo">
						<a href="javascript:void(0)" onclick="realty.getById(<?=$item->id?>)" title="Открыть объявление">
						<img width="170" height="100" src="<?=$item->photos ? $item->photos[0]->link: ""?>" />
						</a>
					</div>
				</div>
				<div class="item-group-2">
					<div class="item-data-photo-gallery">%photoGallery%</div>
				</div>
				<div class="item-group-3">
					<div class="item-data-location">
						<div class="item-data-location-country">
							<a href="javascript:void(0)" onclick="realty.getById(<?=$item->id?>)" title="Открыть объявление"><?=$item->type?>, <?=$item->country;?></a>
						</div>
						<div class="item-data-location-address">
							<a href="javascript:void(0)" onclick="overhillMap.to(<?=$item->lat?>, <?=$item->lon?>)" title="Показать на карте"><?=$item->address?></a>
						</div>
					</div>
					<div class="item-data-info">
						<div class="item-data-info-price"><span class="number"><?=number_format ($item->price,0,"."," ")?></span> 
						<?php 
							if ($item->currency == "RUB")
								echo '<i class="fa fa-rub"></i>';
							elseif ($item->currency == "EUR")
								echo '<i class="fa fa-eur"></i>';
							elseif ($item->currency == "USD")
								echo '<i class="fa fa-usd"></i>';
						?> 
						<span class="note"><?=$item->deal == "аренда" ? "в месяц" : ""?></span></div>
						<div class="item-data-info-area"><span class="number"><?=$item->area?></span> м²</div>
						<div class="item-data-info-price-per-meter"><span class="number"><?=number_format($item->price / $item->area, 2, '.', ' ')?></span> <?php 
					if ($item->currency == "RUB")
						echo '<i class="fa fa-rub"></i>';
					elseif ($item->currency == "EUR")
						echo '<i class="fa fa-eur"></i>';
					elseif ($item->currency == "USD")
						echo '<i class="fa fa-usd"></i>';
				?>/м²</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="item-group-4">
					<div class="item-data-description"><?=$item->address?></div>
				</div>
				<div class="clear"></div>

				<div class="clear"></div>
			</div>

<?endforeach; ?>
<div style="margin-bottom:30px; display: inline-block">
<?= LinkPager::widget([
        'pagination' => $pagination,
        'linkOptions' => [
            'class' => 'realtyPage'
        ],
        'options' => [
            'class' => 'pagination'
        ],
    ]) ?>
</div>
<?php Pjax::end(); ?>
