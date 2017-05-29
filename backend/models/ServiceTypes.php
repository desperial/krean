<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "service_types".
 *
 * @property integer $id_service_types
 */
class ServiceTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_service_types' => 'Id Service Types',
        ];
    }
}
