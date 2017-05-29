<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "role_permissions".
 *
 * @property integer $id_role_permissions
 * @property string $permission
 *
 * @property UserPermissions[] $userPermissions
 */
class RolePermissions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_permissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['permission'], 'required'],
            [['permission'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_role_permissions' => 'Id Role Permissions',
            'permission' => 'Permission',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPermissions()
    {
        return $this->hasMany(UserPermissions::className(), ['permission_id' => 'id_role_permissions']);
    }
}
