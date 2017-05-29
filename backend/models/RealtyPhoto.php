<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "realty_photo".
 *
 * @property integer $id_realty_photo
 * @property integer $realty_id
 * @property string $photo_url
 *
 * @property Realty $realty
 */
class RealtyPhoto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'realty_photo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['realty_id', 'photo_url'], 'required'],
            [['realty_id'], 'integer'],
            [['photo_url'], 'string', 'max' => 150],
            [['realty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Realty::className(), 'targetAttribute' => ['realty_id' => 'id_realty']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_realty_photo' => 'Id Realty Photo',
            'realty_id' => 'Realty ID',
            'photo_url' => 'Photo Url',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealty()
    {
        return $this->hasOne(Realty::className(), ['id_realty' => 'realty_id']);
    }
}
