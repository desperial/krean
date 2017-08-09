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

    public function rules()
    {
        return [
        ];
    }
}