<?

use yii\widgets\LinkPager;

?>
<? foreach ($realty as $item) : ?>
    <div class="realty-item">
        <div class="realty-photo">
            <img src="<?=$item->photos ?  (substr($item->photos[0]->link, 0, 4) == "http" ? $item->photos[0]->link : Yii::getAlias("@uploadsWeb").$item->photos[0]->link) : "" ?>"/>
        </div>
        <div class="top-realty-row">
            <div class="realty-name" data-name="<?=$item->address?>" data-map="<?=($item->lat && $item->lon) ? '1': '0'?>" data-lat="<?=$item->lat?>" data-lon="<?=$item->lon?>">
                <?= $item->address ?> <?=($item->lat && $item->lon) ? '<img src="/imgs/192295-200.png" style="width: 28px; height: 28px;"/>' : ''?>
            </div>
            <? $currency = "₽";
            if ($item->currency == "EUR")
                $currency = "€";
            elseif ($item->currency == "USD")
                $currency = "$";
            ?>
            <div class="realty-price">от <?= number_format($item->price, 0, ".", " ") . " " . $currency ?></div>
            <div class="clear"></div>
        </div>
        <div class="realty-params">
            <div class="realty-params-block">
                <div class="realty-params-left">
                    <div class="realty-param">Площадь:</div>
                    <div class="realty-value"><?= $item->area ?> кв.м.</div>
                    <div class="realty-param">Этажность:</div>
                    <div class="realty-value"><?= $item->floors ?></div>
                    <div class="realty-param">Тип:</div>
                    <div class="realty-value"><?= $item->type ?></div>
                </div>
                <div class="realty-params-right">
                    <div class="realty-param">Подробно:</div>
                    <div class="realty-value"><a href="<?= $item->site_link ?>"><?= $item->site ?></a></div>
                    <div class="realty-param">Владелец:</div>
                    <div class="realty-value"><a href="<?= $item->site_link ?>"><?= $item->site ?></a></div>
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
                    <?= $item->description ?>
                </div>
                <div class="realty-prices">
                    <div class="realty-site"><a href="<?= $item->site_link ?>"><?= $item->site ?></a></div>
                    <div class="realty-price-site"><?= number_format($item->price, 0, ".", " ") . " " . $currency ?></div>

                </div>
            </div>
        </div>
    </div>
    <div class="realty-params-end"></div>
<? endforeach; ?>
<div class="pagination-wrap">
    <?= LinkPager::widget([
        'pagination' => $pages,
    ]); ?>
</div>
