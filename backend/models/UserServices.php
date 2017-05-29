<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_services".
 *
 * @property integer $id_user_services
 * @property integer $service_id
 * @property integer $user_id
 * @property integer $actions
 *
 * @property User $user
 * @property Services $service
 */
class UserServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_id', 'user_id'], 'required'],
            [['service_id', 'user_id', 'actions'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'id_services']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user_services' => 'Id User Services',
            'service_id' => 'Service ID',
            'user_id' => 'User ID',
            'actions' => 'Actions',
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
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id_services' => 'service_id']);
    }
}
