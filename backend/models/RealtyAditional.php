<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "realty_aditional".
 *
 * @property integer $aditional_id
 * @property string $description
 *
 * @property Realty $aditional
 */
class RealtyAditional extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'realty_aditional';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aditional_id', 'description'], 'required'],
            [['aditional_id'], 'integer'],
            [['description'], 'string'],
            [['aditional_id'], 'exist', 'skipOnError' => true, 'targetClass' => Realty::className(), 'targetAttribute' => ['aditional_id' => 'id_realty']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aditional_id' => 'Aditional ID',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAditional()
    {
        return $this->hasOne(Realty::className(), ['id_realty' => 'aditional_id']);
    }
}
