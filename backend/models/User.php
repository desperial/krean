<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $first_name
 * @property string $second_name
 * @property string $last_name
 * @property integer $user_role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Articles[] $articles
 * @property Realty[] $realties
 * @property Robokassa[] $robokassas
 * @property UserRoles $userRole
 * @property UserServices[] $userServices
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_key', 'password_hash', 'email'], 'required'],
            [['user_role', 'status', 'created_at', 'updated_at'], 'integer'],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['first_name', 'second_name', 'last_name'], 'string', 'max' => 45],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['user_role'], 'exist', 'skipOnError' => true, 'targetClass' => UserRoles::className(), 'targetAttribute' => ['user_role' => 'id_user_roles']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'first_name' => 'First Name',
            'second_name' => 'Second Name',
            'last_name' => 'Last Name',
            'user_role' => 'User Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Articles::className(), ['author' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealties()
    {
        return $this->hasMany(Realty::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobokassas()
    {
        return $this->hasMany(Robokassa::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRole()
    {
        return $this->hasOne(UserRoles::className(), ['id_user_roles' => 'user_role']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserServices()
    {
        return $this->hasMany(UserServices::className(), ['user_id' => 'id']);
    }
}
