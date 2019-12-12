<?
use yii\widgets\LinkPager;
?>
<div class="realty-item">
    <div class="realty-photo">
        <img src="/imgs/maxresdefault.jpg"/>
    </div>
    <div class="top-realty-row">
        <div class="realty-name">Бунгало, Кипр, какой-то регион <img src="/imgs/192295-200.png"
                                                                     style="width: 28px; height: 28px;"/>
        </div>
        <div class="realty-price">от 20 000 000 ₽</div>
        <div class="clear"></div>
    </div>
    <div class="realty-params">
        <div class="realty-params-block">
            <div class="realty-params-left">
                <div class="realty-param">Площадь:</div>
                <div class="realty-value">120 кв.м.</div>
                <div class="realty-param">Этажность:</div>
                <div class="realty-value">3</div>
                <div class="realty-param">Тип:</div>
                <div class="realty-value">Бунгало</div>
            </div>
            <div class="realty-params-right">
                <div class="realty-param">Подробно:</div>
                <div class="realty-value"><a href="https://prian.ru">Prian.ru</a></div>
                <div class="realty-param">Владелец:</div>
                <div class="realty-value"><a href="https://prian.ru">Prian.ru</a></div>
            </div>
        </div>
    </div>
    <div class="bottom-realty-row">
        <div href="javascript:void(0)" class="btn btn-success realty-more-info">Подробно</div>
    </div>
    <div class="realty-additional-info">
        <div class="realty-params-end"></div>
        <div class="realty-additional-info-grid">
            <div class="realty-description">
                Красивая вилла с 3 спальнями, 2 ванными комнатами. С прекрасным видом на море. В одном из
                самых престижных районов Торревьеха. С подогревом джакузи на террасе. Сад 250 метров.
            </div>
            <div class="realty-prices">
                <div class="realty-site"><a href="https://prian.ru">Prian.ru</a></div>
                <div class="realty-price-site">20 000 000 ₽</div>
                <div class="realty-site"><a href="https://homesoverseas.ru">Homesoverseas.ru</a></div>
                <div class="realty-price-site">21 000 000 ₽</div>
                <div class="realty-site"><a href="https://avito.ru">Avito.ru</a></div>
                <div class="realty-price-site">24 000 000 ₽</div>
            </div>
        </div>
    </div>
</div>
<div class="realty-params-end"></div>
<div class="realty-item">
    <div class="realty-photo">
        <img src="/imgs/maxresdefault.jpg"/>
    </div>
    <div class="top-realty-row">
        <div class="realty-name">Бунгало, Кипр, какой-то регион <img src="/imgs/192295-200.png"
                                                                     style="width: 28px; height: 28px;"/>
        </div>
        <div class="realty-price">от 20 000 000 ₽</div>
        <div class="clear"></div>
    </div>
    <div class="realty-params">
        <div class="realty-params-block">
            <div class="realty-params-left">
                <div class="realty-param">Площадь:</div>
                <div class="realty-value">120 кв.м.</div>
                <div class="realty-param">Этажность:</div>
                <div class="realty-value">3</div>
                <div class="realty-param">Тип:</div>
                <div class="realty-value">Бунгало</div>
            </div>
            <div class="realty-params-right">
                <div class="realty-param">Подробно:</div>
                <div class="realty-value"><a href="https://prian.ru">Prian.ru</a></div>
                <div class="realty-param">Владелец:</div>
                <div class="realty-value"><a href="https://prian.ru">Prian.ru</a></div>
            </div>
        </div>
    </div>
    <div class="bottom-realty-row">
        <div href="javascript:void(0)" class="btn btn-success realty-more-info">Подробно</div>
    </div>
    <div class="realty-additional-info">
        <div class="realty-params-end"></div>
        <div class="realty-additional-info-grid">
            <div class="realty-description">
                Красивая вилла с 3 спальнями, 2 ванными комнатами. С прекрасным видом на море. В одном из
                самых престижных районов Торревьеха. С подогревом джакузи на террасе. Сад 250 метров.
            </div>
            <div class="realty-prices">
                <div class="realty-site"><a href="https://prian.ru">Prian.ru</a></div>
                <div class="realty-price-site">20 000 000 ₽</div>
                <div class="realty-site"><a href="https://homesoverseas.ru">Homesoverseas.ru</a></div>
                <div class="realty-price-site">21 000 000 ₽</div>
                <div class="realty-site"><a href="https://avito.ru">Avito.ru</a></div>
                <div class="realty-price-site">24 000 000 ₽</div>
            </div>
        </div>
    </div>
</div>

<?=LinkPager::widget([
    'pagination' => $pages,
]);?>