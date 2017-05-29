<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "realty_services".
 *
 * @property integer $id_realty_services
 * @property integer $service_id
 * @property integer $realty_id
 * @property integer $actions
 *
 * @property Realty $realty
 * @property Services $service
 */
class RealtyServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'realty_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'realty_id'], 'required'],
            [['service_id', 'realty_id', 'actions'], 'integer'],
            [['realty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Realty::className(), 'targetAttribute' => ['realty_id' => 'id_realty']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'id_services']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_realty_services' => 'Id Realty Services',
            'service_id' => 'Service ID',
            'realty_id' => 'Realty ID',
            'actions' => 'Actions',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealty()
    {
        return $this->hasOne(Realty::className(), ['id_realty' => 'realty_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id_services' => 'service_id']);
    }
}
