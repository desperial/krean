<?php
use yii\helpers\Html;
use himiklab\colorbox\Colorbox;
setlocale(LC_TIME,'ru_RU');
?>
<?php foreach ($realty as $item): ?>
<div class="item">
	<div class="item-left">
		<div class="item-photos">
			<div class="simple-slider">
				<a href="javascript:void(0)" class="prev">
					<i class="fa fa-angle-left"></i>
				</a>
				<a href="javascript:void(0)" class="next">
					<i class="fa fa-angle-right"></i>
				</a>
				<ul>
			<?php foreach ($item->photos as $photo) :?>
				<li>
					<a class="colorbox" rel="realty-photographies" href="<?=$photo->link?>" target="_blank">
						<img src="<?=$photo->link?>" width="350" height="263" />
					</a>
				</li>
			<? endforeach; ?>
				</ul>
			</div>
			<?= Colorbox::widget([
			    'targets' => [
			        '.colorbox' => [
			            'maxWidth' => '100%',
			            'maxHeight' => '100%',
			        ],
			    ],
			    'coreStyle' => 2
			]) ?>
		</div>
		<div class="seller-ads">
			<div class="seller-ads-header">Продавец</div>
            <div class="seller-ads-contact-name"><i class="fa fa-user"></i>&nbsp;&nbsp;<a href="<?=$item->site_link?>"><?=$item->site;?></a></div>
			<!--<div class="seller-ads-contact-feedback"><i class="fa fa-bullhorn"></i>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="overhillRealty.service.feedback(this, <?=$item->id?>)">Связаться с продавцом</a></div>
			<div class="seller-ads-contact-phone">
				<a href="javascript:void(0)" onclick="overhillRealty.service.requestPhone(<?=$item->id?>, this)" title="Показать номер телефона продавца"><?//=$item->users->contact_phone?></a>
			</div>-->
			<div class="seller-ads-note">Пожалуйста, сообщите продавцу,<br/>что нашли это объявление на Depala.ru</div>
		</div>
		<div class="google-streetview"></div>
		<div class="similar-ads">
			<div class="similar-ads-header">Похожие объявления</div>
			<div class="similar-ads-list"></div>
		</div>
		<div class="banner" style="display:none">
			<img src="web/upload/test-001.png?v2" width="350" height="254" />
		</div>
	</div>
	<div class="item-right">
		<div class="item-header">
			<div class="item-header-title"><?=$item->type?>, <?=$item->country;?></div>
			<div class="item-header-address">
				<a href="javascript:void(0)" onclick="overhillMap.to(<?=$item->lat?>, <?=$item->lon?>)" title="Показать на карте"><?=$item->address?></a>
			</div>
		</div>
		<div class="item-data">
			<div class="item-data-list-left">
				<div class="item-data-list-label">Цена</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=number_format ($item->price,0,"."," ")?> 
				<?php 
					if ($item->currency == "RUB")
						echo '<i class="fa fa-rub"></i>';
					elseif ($item->currency == "EUR")
						echo '<i class="fa fa-eur"></i>';
					elseif ($item->currency == "USD")
						echo '<i class="fa fa-usd"></i>';
				?>  <?=$item->deal == "аренда" ? "в месяц" : ""?></div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Общая площадь</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=$item->area?> м²</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Цена за квадратный метр</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=number_format($item->price / $item->area, 2, '.', ' ')?> <?php 
					if ($item->currency == "RUB")
						echo '<i class="fa fa-rub"></i>';
					elseif ($item->currency == "EUR")
						echo '<i class="fa fa-eur"></i>';
					elseif ($item->currency == "USD")
						echo '<i class="fa fa-usd"></i>';
				?></div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Вид сделки</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=$item->deal?></div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Тип объекта</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=$item->type?></div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Номер объявления</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=$item->id?></div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Количество просмотров</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=$item->shows?></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="item-description"><?=$item->description?></div>
	</div>
	<div class="clear"></div>
</div>
<? endforeach;?>