<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_permissions".
 *
 * @property integer $id_user_permissions
 * @property integer $role_id
 * @property integer $permission_id
 *
 * @property UserRoles $role
 * @property RolePermissions $permission
 */
class UserPermissions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_permissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'permission_id'], 'required'],
            [['role_id', 'permission_id'], 'integer'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRoles::className(), 'targetAttribute' => ['role_id' => 'id_user_roles']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => RolePermissions::className(), 'targetAttribute' => ['permission_id' => 'id_role_permissions']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user_permissions' => 'Id User Permissions',
            'role_id' => 'Role ID',
            'permission_id' => 'Permission ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(UserRoles::className(), ['id_user_roles' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(RolePermissions::className(), ['id_role_permissions' => 'permission_id']);
    }
}
