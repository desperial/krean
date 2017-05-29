<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "services".
 *
 * @property integer $id_services
 * @property string $service_name
 * @property integer $service_price
 * @property string $service_type
 * @property string $service_target
 *
 * @property RealtyServices[] $realtyServices
 * @property UserServices[] $userServices
 */
class Services extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_name', 'service_type', 'service_target'], 'required'],
            [['service_price'], 'integer'],
            [['service_type', 'service_target'], 'string'],
            [['service_name'], 'string', 'max' => 20],
            [['service_name'], 'unique'],
            [['service_type'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_services' => 'Id Services',
            'service_name' => 'Service Name',
            'service_price' => 'Service Price',
            'service_type' => 'Service Type',
            'service_target' => 'Service Target',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealtyServices()
    {
        return $this->hasMany(RealtyServices::className(), ['service_id' => 'id_services']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserServices()
    {
        return $this->hasMany(UserServices::className(), ['service_id' => 'id_services']);
    }
}
