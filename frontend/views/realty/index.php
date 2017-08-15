<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
?>
<?php Pjax::begin(['id' => 'search-form', 'enablePushState' => false, 'scrollTo' => true, 'clientOptions' => ['method' => 'GET']]);  
?>

<a class="overhill-app-right-toggle" href="javascript:void(0)" onclick="overhill.container.list.toggle()" title="Список объявлений"><i class="fa fa-angle-right"></i></a>
<div class="overhill-search-ads-container">
<?php 
    $form = ActiveForm::begin([
	    'id' => 'search-form',
	    'method' => 'get',
	    'options' => [
	    	'data' => ['pjax' => true],
	    	'class' => 'form-horizontal'
    	],
	]);
?>
<?=$form->field($model, 'currency')->hiddenInput(['value'=> "0"])->label(false); ?>
        <div class="overhill-search-ads-basic-container">
            <div class="overhill-search-ads-basic-content">
                <div class="search-ads-group">
                    <div class="search-ads-field search-ads-field-text">
                        <div class="search-ads-legend">Поиск:</div>
                        <?=$form->field($model, 'common', ["template" => "{input}\n{error}"])->textInput(['placeholder' => 'Страна, улица, дом...']) ; ?>
                    </div>
                </div>
                <div class="search-ads-group">
                    <div class="search-ads-field search-ads-field-price">
                        <div class="search-ads-legend">Цена (в выбранной вами валюте):</div>
                        <?=$form->field($model, 'priceFrom', ["template" => "{input}\n{error}"])->textInput(['placeholder' => '0','type'=>'number']) ; ?> &nbsp;&mdash;&nbsp;
						<?=$form->field($model, 'priceTo', ["template" => "{input}\n{error}"])->textInput(['placeholder' => '0','type'=>'number']) ; ?>
                    </div>
                    <div class="search-ads-field search-ads-field-area">
                        <div class="search-ads-legend">Площадь (м²):</div>
                        <?=$form->field($model, 'areaFrom', ["template" => "{input}\n{error}"])->textInput(['placeholder' => '0','type'=>'number']) ; ?> &nbsp;&mdash;&nbsp; 
						<?=$form->field($model, 'areaTo', ["template" => "{input}\n{error}"])->textInput(['placeholder' => '0','type'=>'number']) ; ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="search-ads-group">
                    <div class="search-ads-field search-ads-field-deal">
                    	<?=$form->field($model,'deal', ["template" => "{input}\n{error}"])->radioList([
						    1 => 'Все', 
						    2 => 'Снять',
						    3 => 'Купить'
						]);
                    	?>
                        
                    </div>
                    <div class="search-ads-field search-ads-field-actions">
                        <div class="search-ads-field-on-show-advanced">
                            <a href="javascript:void(0)" onclick="overhill.container.list.toggleAdvanced(this)">Расширенный поиск</a>
                        </div>
                        <div class="search-ads-field-on-reset">
                            <a href="javascript:void(0)" onclick="overhill.realty.list.restart()">Сбросить настройки</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
				        <div class="col-lg-offset-1 col-lg-11">
				            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
				        </div>
				    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="overhill-search-ads-advanced-container">
            <div class="overhill-search-ads-advanced-content">
                <div class="search-ads-group">
                    <div class="search-ads-field search-ads-field-country">
                        <div class="search-ads-legend">Страна:</div>
                        <?php 
                        $countriesArray = [];
                        $countriesArray[0] = "...";
                        foreach ($countries as $country) :
                        	$countriesArray[$country['country']] = $country['name']." (".$country['cnt'].")";
                        endforeach; 
                        ?>
                        <?=$form->field($model, 'country', ["template" => "{input}\n{error}"])->dropDownList($countriesArray);?>
                        
                    </div>
                    <div class="search-ads-field search-ads-field-type">
                        <div class="search-ads-legend">Тип недвижимости:</div>
                        <?=$form->field($model, 'type', ["template" => "{input}\n{error}"])->dropDownList([
                        	0 => "...",
                        	"residental" => "Жилой",
                        	"commercial" => "Коммерческий"
                        ]);?>
                        
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="search-ads-group">
                    <div class="search-ads-field search-ads-field-view">
                        <div class="search-ads-legend">Вид недвижимости:</div>
                        <?=$form->field($model, 'subtype', ["template" => "{input}\n{error}"])->dropDownList([
                        	0 => "...",
                        	"house" => "Дом",
                        	"building" => "Здание",
                        	"land" => "Земельный участок",
                        	"investment" => "Инвестиционный проект",
                        	"apartment" => "Квартира",
                        	"premises" => "Помещение",
                        	"others" => "Прочее",
                        	"townhouse" => "Таунхаус"
                        ]);?>
                        
                    </div>
                    <div class="search-ads-field search-ads-field-group">
                        <div class="search-ads-legend">Социальная группа:</div>
                        <?=$form->field($model, 'group', ["template" => "{input}\n{error}"])->dropDownList([
                        	0 => "...",
                        	"primary" => "Первичная",
                        	"secondary" => "Вторичная"
                        ]);?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    <?php ActiveForm::end(); ?>
</div>

<div class="overhill-list-ads-container">
    <div class="overhill-list-ads-content">
			<?php foreach ($realty as $item): ?>
			        <div class="item ad-from-list-<?=$item->id?>" location:latitude="<?=$item->latitude?>" location:longitude="<?=$item->longitude?>">
							<div class="item-group-1">
								<div class="item-data-photo">
									<a href="javascript:void(0)" onclick="realty.getById(<?=$item->id?>)" title="Открыть объявление">
									<img width="170" height="100" src="/upload/overhill/realty/<?=$item->photos[0]->filename?>" />
									</a>
								</div>
							</div>
							<div class="item-group-2">
								<div class="item-data-photo-gallery">%photoGallery%</div>
							</div>
							<div class="item-group-3">
								<div class="item-data-location">
									<div class="item-data-location-country">
										<a href="javascript:void(0)" onclick="realty.getById(<?=$item->id?>)" title="Открыть объявление"><?=$item['view']?>, <?=$item->countries['name'];?></a>
									</div>
									<div class="item-data-location-address">
										<a href="javascript:void(0)" onclick="overhillMap.to(<?=$item->latitude?>, <?=$item->longitude?>)" title="Показать на карте"><?=$item->address?></a>
									</div>
								</div>
								<div class="item-data-info">
									<div class="item-data-info-price"><span class="number"><?=number_format ($item->price,0,"."," ")?></span> 
									<?php 
										if ($item->currency == "RUR")
											echo '<i class="fa fa-rub"></i>';
										elseif ($item->currency == "EUR")
											echo '<i class="fa fa-eur"></i>';
										elseif ($item->currency == "USD")
											echo '<i class="fa fa-usd"></i>';
									?> 
									<span class="note"><?=$item->deal == "аренда" ? "в месяц" : ""?></span></div>
									<div class="item-data-info-area"><span class="number"><?=$item->area?></span> м²</div>
									<div class="item-data-info-price-per-meter"><span class="number"><?=number_format($item->price / $item->area, 2, '.', ' ')?></span> <?php 
								if ($item->currency == "RUR")
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

			<?= LinkPager::widget(['pagination' => $pagination, 'linkOptions' => ['class' => 'realtyPage']]) ?>
			<?php Pjax::end(); ?>
    </div>
    <a class="overhill-list-ads-on-more" style="display:none;" href="javascript:overhill.realty.list.unload()">Показать ещё 12 объявлений</a>
</div>
