<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "robokassa".
 *
 * @property integer $id_robokassa
 * @property integer $user_id
 * @property integer $realty_id
 * @property string $operation
 * @property string $signature
 * @property string $price
 * @property string $status
 * @property string $date_timestamp
 *
 * @property User $user
 * @property Realty $realty
 */
class Robokassa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'robokassa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'operation', 'signature'], 'required'],
            [['user_id', 'realty_id'], 'integer'],
            [['price'], 'number'],
            [['status'], 'string'],
            [['date_timestamp'], 'safe'],
            [['operation', 'signature'], 'string', 'max' => 45],
            [['signature'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['realty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Realty::className(), 'targetAttribute' => ['realty_id' => 'id_realty']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_robokassa' => 'Id Robokassa',
            'user_id' => 'User ID',
            'realty_id' => 'Realty ID',
            'operation' => 'Operation',
            'signature' => 'Signature',
            'price' => 'Price',
            'status' => 'Status',
            'date_timestamp' => 'Date Timestamp',
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
    public function getRealty()
    {
        return $this->hasOne(Realty::className(), ['id_realty' => 'realty_id']);
    }
}
