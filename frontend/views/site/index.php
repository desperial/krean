<?php

/* @var $this yii\web\View */

$this->title = 'Krean.ru - вся недвижимость, в одном месте!';
?>
<div class="overhill-app">
    <div class="overhill-app-left">
        <div id="overhill-map"></div>
        <div id="overhill-page"></div>
        <div class="overhill-app-tabs">
            <div class="overhill-app-tabs-bar">
                <a class="toggle active" href="javascript:void(0)" rel="1" onclick="overhill.bottomTabsControl(this)">Приоритетные объявления</a>
            </div>
            <div class="overhill-app-tabs-box">
                <div class="tab tab-1 active" id="overhill-vip-ads"></div>
            </div>
        </div>
    </div>

    <div class="overhill-app-right">
        <a class="overhill-app-right-toggle" href="javascript:void(0)" onclick="overhill.container.list.toggle()" title="Список объявлений"><i class="fa fa-angle-right"></i></a>
        <div class="overhill-search-ads-container">
            <form id="overhill-search-ads-form" onsubmit="return false">
                <div class="overhill-search-ads-basic-container">
                    <div class="overhill-search-ads-basic-content">
                        <div class="search-ads-group">
                            <div class="search-ads-field search-ads-field-text">
                                <div class="search-ads-legend">Поиск:</div>
                                <input id="search-ads-field-text" type="text" placeholder="Страна, улица, дом..." />
                            </div>
                        </div>
                        <div class="search-ads-group">
                            <div class="search-ads-field search-ads-field-price">
                                <div class="search-ads-legend">Цена (в выбранной вами валюте):</div>
                                <input id="search-ads-field-price-from" type="text" size="9" value="0" />&nbsp;&mdash;&nbsp;<input id="search-ads-field-price-to" type="text" size="9" value="0" />
                            </div>
                            <div class="search-ads-field search-ads-field-area">
                                <div class="search-ads-legend">Площадь (м²):</div>
                                <input id="search-ads-field-area-from" type="text" size="9" value="0" />&nbsp;&mdash;&nbsp;<input id="search-ads-field-area-to" type="text" size="9" value="0" />
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="search-ads-group">
                            <div class="search-ads-field search-ads-field-deal">
                                <div class="search-ads-field-deal-all">
                                    <span class="search-ads-label">Все</span><input type="radio" checked="checked" name="deal" value="" onchange="overhill.realty.list.setOption(this.name, this.value)" />
                                </div>
                                <div class="search-ads-field-deal-rent">
                                    <span class="search-ads-label">Снять</span><input type="radio" name="deal" value="rent" onchange="overhill.realty.list.setOption(this.name, this.value)" />
                                </div>
                                <div class="search-ads-field-deal-buy">
                                    <span class="search-ads-label">Купить</span><input type="radio" name="deal" value="buy" onchange="overhill.realty.list.setOption(this.name, this.value)" />
                                </div>
                                <div class="clear"></div>
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
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div class="overhill-search-ads-advanced-container">
                    <div class="overhill-search-ads-advanced-content">
                        <div class="search-ads-group">
                            <div class="search-ads-field search-ads-field-country">
                                <div class="search-ads-legend">Страна:</div>
                                <select id="search-ads-container-for-counties" name="country" onchange="overhill.realty.list.setOption(this.name, this.value)" disabled="disabled">
                                    <option value="" selected="selected">...</option>
                                </select>
                            </div>
                            <div class="search-ads-field search-ads-field-type">
                                <div class="search-ads-legend">Тип недвижимости:</div>
                                <select name="type" onchange="overhill.realty.list.setOption(this.name, this.value)">
                                    <option value="" selected="selected">...</option>
                                    <option value="residential">Жилой</option>
                                    <option value="commercial">Коммерческий</option>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="search-ads-group">
                            <div class="search-ads-field search-ads-field-view">
                                <div class="search-ads-legend">Вид недвижимости:</div>
                                <select name="view" onchange="overhill.realty.list.setOption(this.name, this.value)">
                                    <option value="" selected="selected">...</option>
                                    <option value="house">Дом</option>
                                    <option value="building">Здание</option>
                                    <option value="land">Земельный участок</option>
                                    <option value="investment">Инвестиционный проект</option>
                                    <option value="apartment">Квартира</option>
                                    <option value="premises">Помещение</option>
                                    <option value="others">Прочее</option>
                                    <option value="townhouse">Таунхаус</option>
                                </select>
                            </div>
                            <div class="search-ads-field search-ads-field-group">
                                <div class="search-ads-legend">Социальная группа:</div>
                                <select name="group" onchange="overhill.realty.list.setOption(this.name, this.value)">
                                    <option value="" selected="selected">...</option>
                                    <option value="primary">Первичная</option>
                                    <option value="secondary">Вторичная</option>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </form>
        </div>
        <div class="overhill-list-ads-container">
            <div class="overhill-list-ads-content"></div>
            <a class="overhill-list-ads-on-more" style="display:none;" href="javascript:overhill.realty.list.unload()">Показать ещё 12 объявлений</a>
        </div>
    </div>
    <div class="overhill-ad-container">
        <div class="overhill-ad-wrapper">
            <div class="overhill-ad-content"></div>
            <a class="overhill-ad-close" href="javascript:void(0)" onclick="overhill.container.show.close()" title="Закрыть страницу"></a>
        </div>
        <a class="overhill-ad-toggle" href="javascript:void(0)" onclick="overhill.container.show.toggle()" title="Объявление"><i class="fa fa-angle-right"></i></a>
    </div>
</div>

<script>
      function initMap() {
        
      }
</script>

<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'n0LnCHlwtD';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- {/literal} END JIVOSITE CODE -->