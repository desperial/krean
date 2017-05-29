<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_roles".
 *
 * @property integer $id_user_roles
 * @property string $role_name
 * @property string $role_permissions
 *
 * @property User[] $users
 * @property UserPermissions[] $userPermissions
 */
class UserRoles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_name', 'role_permissions'], 'required'],
            [['role_name', 'role_permissions'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user_roles' => 'Id User Roles',
            'role_name' => 'Role Name',
            'role_permissions' => 'Role Permissions',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['user_role' => 'id_user_roles']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPermissions()
    {
        return $this->hasMany(UserPermissions::className(), ['role_id' => 'id_user_roles']);
    }
}
