<?php

namespace console\controllers;

use common\models\Adverts;
use common\models\AdvertsCommerce;
use common\models\ImageUpload;
use yii\console\Controller;
use Yii;
use console\models;

class DaemonController extends Controller
{

    private $parser;
    private $state_config;
    private $site;

    public function actionIndex()
    {
        echo "Yes, cron service is running.";
    }

    //region Задачи демона по парсингу сайтов

    public function actionStartRent($site)
    {
        $this->site = $site;
        $this->startParsing();
    }
    public function actionParse($site)
    {
        $this->site = $site;
        $this->startParsing();
    }
    //endregion

    //region Set Params
    private function prepareData()
    {
        $this->parser = $this->params();
        $this->state_config = $this->getStateConfig();
    }

    private function params()
    {
        if (is_file(Yii::getAlias("@console") . '/parser_configs/' . $this->site . ".json")) {
            $config = json_decode(file_get_contents(Yii::getAlias("@console") . "/parser_configs/" . $this->site . ".json"), false);
            return $config;
        }
        die("Couldn't retrieve config file");
    }

    private function getStateConfig()
    {
        if (!$conf = models\ParserConfig::find()->where(['=', 'site_name', $this->site])->one()) {
            $conf = new models\ParserConfig();
            $conf->site_name = $this->site;
            $conf->save(false);
        }
        return $conf;
    }

    //endregion

    public function actionRegenerateCharcodes()
    {
        $adverts = Adverts::find()->innerJoin("(SELECT charcode AS cc
               FROM   adverts
               GROUP  BY charcode
               HAVING COUNT(advert_id) > 1) dup", "adverts.charcode = dup.cc")->all();
        $count = 0;
        if ($adverts) {
            foreach ($adverts as $item) {
                $item->charcode = $item->createCharcode($item->name);
                $item->save(false);
                $count++;
            }
            echo $count." objects renamed\n";
        }

    }

    private function startParsing($debug = false)
    {
        $this->prepareData();
        $instance = new models\ParserInstance($this->parser, $this->state_config,$debug);
        while ($instance->continue) {
            $this->state_config = $instance->state_config;
            unset($instance);
            gc_collect_cycles();
            $instance = new models\ParserInstance($this->parser, $this->state_config,$debug);
        }
    }

    private function parseBuilders($debug = false)
    {
        $instance = new models\ParserBuildersInstance();
    }

    //region Остальные задачи Демона
    public function actionReverseGeocoding()
    {
        $this->startGeocoding();

    }



    //endregion

    private function startGeocoding()
    {
        $config = json_decode(file_get_contents(Yii::getAlias("@console") . "/geo.json"), false);
        $objects = Adverts::find()
            ->where([
                'and',
                ['<>', 'lat', 0],
                ['<>', 'lon', 0],
                ['is', 'settlement',null]
            ])
            ->orderBy("advert_id ASC")
            ->offset($config->offset)
            ->limit(100)
            ->all();
        if ($objects) {
            $count = 0;
            $missed = 0;
            foreach ($objects as $object) {
                $map_object = json_decode(file_get_contents("https://geocode-maps.yandex.ru/1.x?geocode=" . $object->lon . "," . $object->lat . "&apikey=0d0cc08f-0214-4d1e-980c-8cabef3747d6&format=json&kind=house"), false);
                if ($map_object->response->GeoObjectCollection && is_array($map_object->response->GeoObjectCollection->featureMember) && array_key_exists(0, $map_object->response->GeoObjectCollection->featureMember)) {
                    foreach ($map_object->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->Address->Components as $address_node) {
                        switch ($address_node->kind) {
                            case "province":
                                $object->region = $address_node->name;
                                break;
                            case "locality":
                                $object->settlement = $address_node->name;
                                break;
                            case "street":
                                $object->throughfare = $address_node->name;
                                break;
                            case "house":
                                $object->premise = $address_node->name;
                                break;
                        };
                    }
                    $count++;
                    $object->save(false);
                }
                else {
                    $missed++;
                }
            }
            $config->iteration++;
            $config->offset += $missed;
            if ($config->iteration != 150) {
                $jsondata = json_encode($config, JSON_PRETTY_PRINT);
                if (file_put_contents(Yii::getAlias("@console") . "/geo.json", $jsondata)) {
                    echo 'objects converted: '. $count."\n";
                    $this->startGeocoding();
                } else
                    echo '{"error": "something happend!"}';
            }
            else {
                $config->iteration = 0;
                $jsondata = json_encode($config, JSON_PRETTY_PRINT);
                if (file_put_contents(Yii::getAlias("@console") . "/geo.json", $jsondata)) {
                    echo 'objects converted: '. $count.'\n Ending sequence';
                } else
                    echo '{"error": "something happend!"}';
            }
        }
        else {
            $config->offset = 0;
            $config->iteration = 0;
            $jsondata = json_encode($config, JSON_PRETTY_PRINT);
            if (file_put_contents(Yii::getAlias("@console") . "/geo.json", $jsondata)) {
                echo 'No more objects. Ending sequence';
            } else
                echo '{"error": "something happend!"}';
        }
    }

    public function actionCurrencyConversion()
    {
        $curr = file_get_contents("https://www.cbr-xml-daily.ru/daily_json.js");
        $curr = json_decode($curr);
        $adverts = Adverts::find()->where(['<>','currency','RUB'])->all();
        foreach ($adverts as $item) {
            $item->price = $item->price_in_currency * $curr->Valute->{$item->currency}->Value;
            $item->save(false);
        }
    }

    public function actionClearEmptyPhotos()
    {
        $adverts = Adverts::find()->where(["NOT LIKE", 'source','rentatime'])->all();
        $counter = 0;
        foreach ($adverts as $item) {
            if (is_array($item->advertsPhotos)) {
                foreach ($item->advertsPhotos as $photo) {
                    if (!ImageUpload::checkRemoteFile($photo->photo)) {
                        $photo->delete();
                        $counter++;
                    }
                }
            }
        }
        echo $counter." photos deleted\n";
    }

    public function actionAutoresetCount()
    {
         $users = \common\models\User::find()->all();
        foreach($users as $user){
            $user->research_count=0;
            echo $user->save(false)
                ?"Счетчик пользователя $user->username сброшен\n"
                :"Ошибка! \n";
        }
        return true;

    }

    public function actionParseBuilders()
    {
        $this->parseBuilders();
    }
    public function actionArchiveObjects()
    {
        $adverts = Adverts::find()->where('created_time < (unix_timestamp() - 2600000)')->andWhere(['=','archived','0'])->limit(1000)->all();
        $count = 0;
        foreach ($adverts as $advert) {
            $advert->archived = 1;
            $advert->archived_time = time();
            $advert->save(false);
            $count++;
        }
        echo $count." objects archived\n";
        if ($count > 0)
            $this->actionArchiveObjects();
    }


    public function actionUnarchiveObjects()
    {
        $adverts = Adverts::find()->where('created_time > (unix_timestamp() - 2600000)')->andWhere(['=','archived','1'])->limit(1000)->all();
        $count = 0;
        foreach ($adverts as $advert) {
            $advert->archived = 0;
            $advert->archived_time = null;
            $advert->save(false);
            $count++;
        }
        echo $count." objects unarchived\n";
        if ($count > 0)
            $this->actionUnarchiveObjects();
    }

    public function actionGetBusyObjects()
    {
        $advertsCommerce = AdvertsCommerce::find()->where(
            'description REGEXP 
            "пятерочк|Магнит|Монетка|Красное и Белое|Инвестиции|Арендный бизнес|Арендатор|Арендаторы|Готовый бизнес|Продажа бизнеса|Дикси|Fix Price|Окей|Метро|Лента|Бристоль|Фасоль|Эльдорадо|М видео|Детский мир|Аптека|Магазин|Светофор|Атак|Перекресток|Авоська|Виктория|Магнолия|Billa|Мария Ра|Красный Яр|Бахетле|Полушка|Утконос|Азбукп вкуса|Глобус Гурмэ|Семья|Молния|Spar|Линия|Европа|Слата|Эссен|Мираторг|Оливье|Самбери|Ярче|ВкусВилл|Да!|Покупочка|Доброцен|Удача|Эконом|ФрешФрост|Семейный|Адамас|585|Rbt|Dns"
            ')->andWhere(["IS", 'quarters_bisy',null])->limit(5000);
        $counter = 0;
        foreach ($advertsCommerce->each(100) as $row) {
            $counter++;
            $row->quarters_bisy = 1;
            $row->save(false);
        }
        echo $counter." objects marked as busy";
    }

    public function actionGenerateAdvertsSearch()
    {
        Yii::$app->db->createCommand("
        REPLACE LOW_PRIORITY INTO helper_adverts_search 
        (advert_id,price,area_forsale,object_type,charcode,photo,`show`,archived,deleted,author,created_time,region,settlement,adress,lat,lon,`source`,quarters_bisy,moderated,tel,upped,per_month) 
        SELECT  
            advert_id,
            price,
            area_forsale,
            adverts_commerce.object_type,
            charcode,
            (SELECT photo FROM adverts_photos WHERE advert = advert_id LIMIT 1) as `photo`,
            `show`,
            archived,
            deleted,
            author,
            created_time,
            region,
            settlement,
            adress,
            lat,
            lon,
            `source`,
            quarters_bisy,
            moderated,
            tel,
            upped,
            per_month
        FROM
            `adverts`
        LEFT JOIN
            `adverts_commerce` ON `adverts`.`advert_id` = `adverts_commerce`.`id`;
        ")->execute();
    }

    public function actionParseRetailers()
    {
        $instance = new models\ParserRetailersInstance();
    }

    public function actionParseFranchises()
    {
        $instance = new models\ParserFranchisesInstance();
    }

    public function actionParseAgencies()
    {
        $this->site = "cian_agencies";
        $this->state_config = $this->getStateConfig();
        $instance = new models\ParserAgenciesInstance($this->state_config);
    }
}

/*
Описание свойств json-файла с конфигом для сайта:
site - обязательное свойство, имя сайта
parse_link - обязательное свойство, ссылка на список объектов (листаемый), откуда берем данные
parse_shift - сдвиг парсера, большинство сайтов передает номер страницы, но некоторые - номер начального объекта.
link_prefix - префикс для ссылки на страницу объекта. Как правило, необходимо добавить "https://"
parse_listing - устаревшее свойство, уже нигде не использую
object_selector - селектор объекта, в котором содержится ссылка
object_link_selector - селектор ссылки на объект, отсюда берется аттрибут "href"
add_site_to_object_link - true/false, иногда необходимо добавлять название сайта к ссылке объекта (при относительных ссылках)
image_selector - селектор изображений объекта
image_src_attr - на случай, если у изображения указан не атрибут "src", а, например, "data-src"
site_for - сайт, для которого мы делаем парсинг (от этого зависит кол-во атрибутов)
city_addition - массив ссылок на страницы по городам для сайтов, где их получение автоматикой затруднено (domofond.ru)
parse_link_types - массив ссылок на страницы с типом недвижимости
    link - ссылка на тип недвижимости
    text - Название типа, записываемое в БД.
fields - список полей и их значений/атрибутов для парсинга
    field - имя поля (используется в т.ч. для записи в БД
    parse_tag - селектор, содержащий значение, либо предыдущий, при наличии свойства fixed рекомендуется выставлять в false
    parse_cond - поиск по условию, для парсинга среди списка одинаковых объектов (например, если у нас есть несколько тэгов "td", мы ищем цену, соответственно, ставим значение поля "Цена". Ну, или аналог, в зависимости от того, как оно написано на сайте)
    attribute - если нужно брать не содержимое селектора, а значение его определенного атрибута
    match - условие для поиска по регулярному выражению. Например, спарсив значение цены, мы получаем сырой результат типа "цена 15 000 320 руб.", дабы получить готовое значение без "цена ... руб." мы и пишем регулярку. работает только со значением конверсии "match"
    fixed - фиксированное значение поля, никакого парсинга не производится, желательно указывать дополнительно parse_tag: false
    conversion - массив обработки полученных данных
        match - используется с атрибутом match массива fields, выполняет поиск по регулярке
        trim - обрезка пробелов в начале и конце строки
        spaces - обрезка пробелов по всей строке
        floatval - получаем значение типа float
        intval - получаем значение типа int
        inner_spaces - на некоторых сайтах кодировка чудит, и символы пробелов выставляются кодами. В базу пишутся аналогично. Эта функция используется для фикса этой херни
        currency - заменяет значки валюты на текст RUB EUR USD для записи в базу.
        type_linked - используется с массивом parse_link_types, чтобы задать значение типа недвижимости согласно значению текущему массива.

Дополнительно:
Данные из всех селекторов берутся аналогом jQuery.text, исключение составляет поле "description", данные из которого берутся аналогом jQuery.html, дабы сохранить оригинальную разметку.

Логика сборки ссылки:
***************
$this->site = $this->config->parse_link;
$this->site_name = $this->config->site;

при наличии списка типов:
    link_prefix + parse_link + type + city + parse_link_suffix
при наличии "link_city_first"
    link_prefix + parse_link + city + type + parse_link_suffix

*/