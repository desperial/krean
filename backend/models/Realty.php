<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "realty".
 *
 * @property integer $id_realty
 * @property integer $user_id
 * @property string $country
 * @property string $curency
 * @property string $price
 * @property string $area
 * @property string $deal
 * @property string $type
 * @property string $view
 * @property string $group
 *
 * @property User $user
 * @property RealtyAditional $realtyAditional
 * @property RealtyPhoto[] $realtyPhotos
 * @property RealtyServices[] $realtyServices
 * @property Robokassa[] $robokassas
 */
class Realty extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'realty';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'country', 'curency', 'price', 'deal', 'type', 'view', 'group'], 'required'],
            [['user_id'], 'integer'],
            [['price', 'area'], 'number'],
            [['deal', 'type', 'view', 'group'], 'string'],
            [['country'], 'string', 'max' => 2],
            [['curency'], 'string', 'max' => 3],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_realty' => 'Id Realty',
            'user_id' => 'User ID',
            'country' => 'Country',
            'curency' => 'Curency',
            'price' => 'Price',
            'area' => 'Area',
            'deal' => 'Deal',
            'type' => 'Type',
            'view' => 'View',
            'group' => 'Group',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealtyAditional()
    {
        return $this->hasOne(RealtyAditional::className(), ['aditional_id' => 'id_realty']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealtyPhotos()
    {
        return $this->hasMany(RealtyPhoto::className(), ['realty_id' => 'id_realty']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealtyServices()
    {
        return $this->hasMany(RealtyServices::className(), ['realty_id' => 'id_realty']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobokassas()
    {
        return $this->hasMany(Robokassa::className(), ['realty_id' => 'id_realty']);
    }
}
