<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Model;

class SearchForm extends \yii\base\Model
{
    public $common;
    public $priceFrom;
    public $priceTo;
    public $areaFrom;
    public $areaTo;
    public $deal;
    public $country;
    public $type;
    public $subtype;
    public $group;
    public $currency;

    public function rules()
    {
        return [
            [['common', 'priceFrom', 'priceTo', 'areaFrom', 'areaTo', 'deal', 'country','type','subtype','group','currency'], 'safe']
        ];
    }
}