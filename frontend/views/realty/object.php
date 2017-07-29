<?php
use yii\helpers\Html;
use himiklab\colorbox\Colorbox;
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
					<a class="colorbox" rel="realty-photographies" href="/upload/overhill/realty/<?=$photo->filename?>" target="_blank">
						<img src="/upload/overhill/realty/<?=$photo->filename?>" width="350" height="263" />
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
			<div class="seller-ads-contact-name"><i class="fa fa-user"></i>&nbsp;&nbsp;<?=$item->user['name'];?></div>
			<div class="seller-ads-contact-feedback"><i class="fa fa-bullhorn"></i>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="overhillRealty.service.feedback(this, <?=$item->id?>)">Связаться с продавцом</a></div>
			<div class="seller-ads-contact-phone">
				<a href="javascript:void(0)" onclick="overhillRealty.service.requestPhone(<?=$item->id?>, this)" title="Показать номер телефона продавца">%user_contact_phone%</a>
			</div>
			<div class="seller-ads-note">Пожалуйста, сообщите продавцу,<br/>что нашли это объявление на Krean.ru</div>
		</div>
		<div class="google-streetview"></div>
		<div class="similar-ads">
			<div class="similar-ads-header">Похожие объявления</div>
			<div class="similar-ads-list"></div>
		</div>
		<div class="banner" style="display:none">
			<img src="' + fenric.url('web/upload/test-001.png?v2') + '" width="350" height="254" />
		</div>
	</div>
	<div class="item-right">
		<div class="item-header">
			<div class="item-header-title"><?=$item['view']?>, %country_name%</div>
			<div class="item-header-address">
				<a href="javascript:void(0)" onclick="overhillMap.to(<?=$item->latitude?>, <?=$item->longitude?>)" title="Показать на карте"><?=$item->address?></a>
			</div>
		</div>
		<div class="item-data">
			<div class="item-data-list-left">
				<div class="item-data-list-label">Цена</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label"><?=$item->price?> <?=$item->currency?> %deal_suffix%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Общая площадь</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%area% м²</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Цена за квадратный метр</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%price_square_meter% %formated_currency%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Вид сделки</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%deal_str%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Тип объекта</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%type_str%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Социальная группа объекта</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%group_str%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Номер объявления</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%id%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Дата создания</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%date_create%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Дата обновления</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%date_update%</div>
			</div>
			<div class="clear"></div>
			<div class="item-data-list-left">
				<div class="item-data-list-label">Количество просмотров</div>
			</div>
			<div class="item-data-list-right">
				<div class="item-data-list-label">%shows%</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="item-description">%description%</div>
	</div>
	<div class="clear"></div>
</div>
<? endforeach;?>