<?php

use yii\widgets\Pjax;
use common\widgets\RealtyWigdet;

/* @var $this yii\web\View */

$this->title = 'Depala.ru - вся зарубежная недвижимость, в одном месте!';
?>
<div class="sf-main-wrap">
    <div class="sf-overlay">
        <div class="sf-center-content" id="search-wrap">
            <form id="realty-search-form" method="get" action="<?= \yii\helpers\Url::to(['realty/list']) ?>">
                <div class="site-name"><a href="<?=\yii\helpers\Url::base(true)?>">Depala.ru</a></div>
                <h3 class="site-title">агрегатор зарубежной недвижимости</h3>
                <h3 class=""><a href="<?=\yii\helpers\Url::to(['/site/contact'])?>" style="color: white; font-weight: bolder">Добавление объектов - бесплатно!</a></h3>
                <div class="search-block">
                    <div class="mat-div country-wrap">
                        <label for="search-ads-container-for-counties" class="mat-label">Страна</label>
                        <select name="country" class="mat-input" id="search-ads-container-for-counties"
                                onchange="overhill.realty.list.setOption(this.name, this.value)">
                            <option value="" selected="selected"></option>
                            <? foreach ($countries as $country) : ?>
                                <option value="<?= $country->country ?>"><?= $country->country ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="mat-div type-wrap">
                        <label for="search-ads-field-view" class="mat-label">Вид недвижимости</label>
                        <select name="type" class="mat-input" id="search-ads-field-view"
                                onchange="overhill.realty.list.setOption(this.name, this.value)">
                            <option value="" selected="selected"></option>
                            <? foreach ($types as $type) : ?>
                                <option value="<?= $type->type ?>"><?= $type->type ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="search-ads-field search-ads-field-price">
                        <div class="search-ads-legend">Цена (в рублях):</div>
                        <div class="two-fields-wrap">
                            <div class="mat-div">
                                <label for="search-ads-field-price-from" class="mat-label">От</label>
                                <input type="text" name="price_from" class="mat-input" id="search-ads-field-price-from" size="9"/>
                            </div>
                            <div class="mat-div">
                                <label for="search-ads-field-price-to" class="mat-label">До</label>
                                <input type="text" name="price_to" class="mat-input" id="search-ads-field-price-to" size="9">
                            </div>
                        </div>
                    </div>

                    <div class="search-ads-field search-ads-field-area">
                        <div class="search-ads-legend">Площадь (м²):</div>
                        <div class="two-fields-wrap">
                            <div class="mat-div">
                                <label for="search-ads-field-area-from" class="mat-label">От</label>
                                <input type="text" name="area_from" class="mat-input" id="search-ads-field-area-from" size="9"/>
                            </div>
                            <div class="mat-div">
                                <label for="search-ads-field-area-to" class="mat-label">До</label>
                                <input type="text" name="area_to" class="mat-input" id="search-ads-field-area-to" size="9">
                            </div>
                        </div>
                    </div>

                    <div class="dlk-radio btn-group sf-position-center">
                        <label class="btn btn-success">
                            <input name="deal" class="form-control" type="radio" value="" checked>
                            <i class="fa fa-check glyphicon glyphicon-ok"></i> Все
                        </label>
                        <label class="btn btn-success">
                            <input name="deal" class="form-control" type="radio" value="rent">
                            <i class="fa fa-check glyphicon glyphicon-ok"></i> Снять
                        </label>
                        <label class="btn btn-success">
                            <input name="deal" class="form-control" type="radio" value="buy">
                            <i class="fa fa-check glyphicon glyphicon-ok"></i> Купить
                        </label>
                    </div>
                    <div class="search-button">
                        <button type="submit"  data-pjax="1" class="btn btn-success"
                           id="search-button"
                           onclick="items.sendRequest();">Искать</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="realty-wrap">
            <?
            Pjax::begin(['formSelector' => '#realty-search-form']);
                if (isset($realty))
                    echo $this->render('/realty/list', [
                        'realty' => $realty,
                        'pages' => $pages
                    ]);
            Pjax::end();
            ?>
        </div>
    </div>

</div>
<div class="modal bs-example-modal-lg modal-dialog" id="map-modal" tabindex="-1" role="dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-modal-id="#map-modal" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="map-modal-title"> </h4>
        </div>
        <div class="modal-body">
            <div id="overhill-map"></div>
        </div>
    </div>
</div>
<script>
    function initMap() {

    }
</script>
<?
$this->registerJs('
    $(".mat-input").focus(function(){
      $(this).parent().addClass("is-active is-completed");
    });
    
    $(".mat-input").focusout(function(){
      if($(this).val() === "")
        $(this).parent().removeClass("is-completed");
      $(this).parent().removeClass("is-active");
    });
    
    $(document).ready(function(){
        $(".mat-input").each(function(){
            if ($(this).val() !== "") {
                $(this).parent().addClass("is-completed");
            }
        });
        '.(isset($realty) ? 'items.searchToTop()' : '').'
    });
');
?>
