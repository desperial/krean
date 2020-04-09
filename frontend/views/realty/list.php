<?

use yii\widgets\LinkPager;

?>
<? foreach ($realty as $item) : ?>
    <div class="row realty-item">
        <div class="col-xs-12 col-sm-3 col-md-4" style="position:relative">
            <img class="disclaimer-image"
                 src="<?= $item->photos ? (substr($item->photos[0]->link, 0, 4) == "http" ? $item->photos[0]->link : Yii::getAlias("@uploadsWeb") . $item->photos[0]->link) : "" ?>"/>
        </div>
        <div class="col-xs-12 col-sm-9 col-md-8">
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="realty-name" data-name="<?= $item->address ?>"
                         data-map="<?= ($item->lat && $item->lon) ? '1' : '0' ?>" data-lat="<?= $item->lat ?>"
                         data-lon="<?= $item->lon ?>">
                        <?= $item->address ?> <?= ($item->lat && $item->lon) ? '<img src="/imgs/192295-200.png" style="width: 28px; height: 28px;"/>' : '' ?>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-4 text-center text-sm-right">
                    <? $currency = "₽";
                    if ($item->currency == "EUR")
                        $currency = "€";
                    elseif ($item->currency == "USD")
                        $currency = "$";
                    ?>
                    <div class="realty-price">от <?= number_format($item->price, 0, ".", " ") . " " . $currency ?></div>
                </div>
                <div class="realty-params col-xs-12 px-0 mx-0">
                    <div class="realty-params-block row">
                        <div class="realty-params-left col-xs-12 col-md-6">
                            <div class="row">
                                <div class="realty-param col-xs-6">Площадь:</div>
                                <div class="realty-value col-xs-6"><?= $item->area ?> кв.м.</div>
                            </div>
                            <div class="row">
                                <div class="realty-param col-xs-6">Этажность:</div>
                                <div class="realty-value col-xs-6"><?= $item->floors ?: "Не указана" ?></div>
                            </div>
                            <div class="row">
                                <div class="realty-param col-xs-6">Тип:</div>
                                <div class="realty-value col-xs-6"><?= $item->type ?></div>
                            </div>
                        </div>
                        <div class="realty-params-right col-xs-12 col-md-6">
                            <div class="row px-0 mx-0">
                                <div class="realty-param col-xs-5 col-sm-5 col-md-12 col-lg-5 px-0">Подробно: </div>
                                <div class="realty-value col-xs-7 col-sm-7 col-md-12 col-lg-7 px-0"> <a href="<?= $item->site_link ?>"><?= $item->site ?></a></div>
                            </div>
                            <div class="row px-0 mx-0">
                                <div class="realty-param col-xs-5 col-sm-5 col-md-12 col-lg-5 px-0">Владелец: </div>
                                <div class="realty-value col-xs-7 col-sm-7 col-md-12 col-lg-7 px-0"> <a href="<?= $item->site_link ?>"><?= $item->site ?></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="realty-item">

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
