<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * ContactForm is the model behind the contact form.
 * @property float $lat
 * @property float $lon
 * @property string $description
 * @property string $site
 * @property string $site_id
 * @property string $site_link
 * @property string $country
 * @property string $region
 * @property string $settlement
 * @property string $throughfare
 * @property string $premise
 * @property float $price
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property float $area
 * @property string $type
 * @property int $enabled
 * @property int $id
 * @property string $currency
 * @property string $deal
 */
class Realty extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return '{{%realty}}';
    }

    public function getPhotos()
    {
        return $this->hasMany(Photo::class, ['realty' => 'id']);
    }

    public function getGeo($save = true)
    {
        if (!$this->lat || !$this->lon)
            return false;
        try {
            $opts = array('http' =>
                array(
                    'method' => 'GET',
                    'ignore_errors' => TRUE,
                )
            );
            $context = stream_context_create($opts);
            if ($map_object = json_decode(file_get_contents("https://geocode-maps.yandex.ru/1.x?geocode=" . $this->lon . "," . $this->lat . "&apikey=0d0cc08f-0214-4d1e-980c-8cabef3747d6&format=json&kind=house", false, $context), false)) {
                if (!property_exists($map_object, "statusCode")) {
                    if ($map_object->response->GeoObjectCollection && is_array($map_object->response->GeoObjectCollection->featureMember) && array_key_exists(0, $map_object->response->GeoObjectCollection->featureMember)) {
                        foreach ($map_object->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->Address->Components as $address_node) {
                            switch ($address_node->kind) {
                                case "country":
                                    $this->country = $address_node->name;
                                    break;
                                case "province":
                                    $this->region = $address_node->name;
                                    break;
                                case "locality":
                                    $this->settlement = $address_node->name;
                                    break;
                                case "street":
                                    $this->throughfare = $address_node->name;
                                    break;
                                case "house":
                                    $this->premise = $address_node->name;
                                    break;
                            };
                        }
                    }
                    if ($save)
                        $this->save(false);
                }
            }
        } catch (\HttpRequestException $e) {
            return false;
        }

    }

    public static function searchRealty()
    {
        $data = Yii::$app->request->get();
        $euro = 71; //TODO убрать ручную заглушку, брать данные из выгрузки центробанка
        $dollar = 64; //TODO убрать ручную заглушку, брать данные из выгрузки центробанка
        $price_from = $data['price_from'] ? $data['price_from']/$euro : 0;
        $price_to = $data['price_to'] ? $data['price_to']/$euro : 999999999999;
        $area_from = $data['area_from'] ? $data['area_from'] : 0;
        $area_to = $data['area_to'] ? $data['area_to'] : 999999999999;
        $search = [];
        if ($data['country'])
            $search['country'] = $data['country'];
        if ($data['type'])
            $search['type'] = $data['type'];
        if ($data['deal'])
            $search['deal'] = $data['deal'];
        $price = ['between', 'price', $price_from, $price_to];
        $area = ['between', 'area', $area_from, $area_to];
        return Realty::find()->where($search)->andWhere($price)->andWhere($area)->orderBy("created_at DESC");
    }
}


class User extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%overhill_users}}';
    }

    public function getRealty()
    {
        return $this->hasMany(Realty::className(), ['user' => 'id']);
    }
}

class Country extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%overhill_countries}}';
    }

    public function getRealty()
    {
        return $this->hasMany(Realty::className(), ['country' => 'code']);
    }
}

class Description extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%overhill_realty_descriptions}}';
    }

    public function getRealty()
    {
        return $this->hasOne(Realty::className(), ['id' => 'realty']);
    }
}