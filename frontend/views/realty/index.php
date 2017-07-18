<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<?php foreach ($realty as $item): ?>
        <div class="item ad-from-list-<?=$item->id?>" location:latitude="<?=$item->latitude?>" location:longitude="<?=$item->longitude?>">
				<div class="item-group-1">
					<div class="item-data-photo">
						<a href="javascript:void(0)" onclick="overhill.realty.getById(<?=$item->id?>)" title="Открыть объявление">
						<img width="170" height="100" src="upload/overhill/realty/<?=$item->photos[0]->filename?>" />
						</a>
					</div>
				</div>
				<div class="item-group-2">
					<div class="item-data-photo-gallery">%photoGallery%</div>
				</div>
				<div class="item-group-3">
					<div class="item-data-location">
						<div class="item-data-location-country">
							<a href="javascript:void(0)" onclick="overhill.realty.getById(<?=$item->id?>)" title="Открыть объявление">%view_str%, %country_name%</a>
						</div>
						<div class="item-data-location-address">
							<a href="javascript:void(0)" onclick="overhillMap.to(<?=$item->latitude?>, <?=$item->longitude?>)" title="Показать на карте"><?=$item->address?></a>
						</div>
					</div>
					<div class="item-data-info">
						<div class="item-data-info-price"><span class="number"><?=$item->price?></span> <?=$item->currency?> <span class="note">%deal_suffix%</span></div>
						<div class="item-data-info-area"><span class="number"><?=$item->area?></span> м²</div>
						<!--<div class="item-data-info-price-per-meter"><span class="number">%price_square_meter%</span> %formated_currency%/м²</div>-->
						<div class="clear"></div>
					</div>
				</div>
				<div class="item-group-4">
					<div class="item-data-description"><?=$item->address?></div>
				</div>
				<div class="clear"></div>
				<!--%is_autor_start%
				<div class="item-groups-separator"></div>
				<div class="item-group-6">
					<div class="item-management">
						<i class="fa fa-shopping-cart"></i>&nbsp;<a href="javascript:void(0)" onclick="overhill.realty.control.vip(%id%)">V.I.P. объявление</a><br/>
						<i class="fa fa-shopping-cart"></i>&nbsp;<a href="javascript:void(0)" onclick="overhill.realty.control.raise(%id%)">Поднять объявление</a>
					</div>
				</div>
				<div class="item-group-7">
					<div class="item-management">
						<i class="fa fa-info-circle"></i>&nbsp;<a href="javascript:void(0)" onclick="overhillRealty.service.info(%id%)">Информация</a><br/>
						<i class="fa fa-pencil-square-o"></i>&nbsp;<a href="javascript:void(0)" onclick="overhill.realty.control.edit(%id%)">Изменить</a><br/>
						<i class="fa fa-times"></i>&nbsp;<a href="javascript:void(0)" onclick="overhill.realty.control.remove(%id%)">Удалить</a>
					</div>
				</div>
				%is_autor_end%-->
				<div class="clear"></div>
			</div>

<?endforeach; ?>

<?= LinkPager::widget(['pagination' => $pagination]) ?>