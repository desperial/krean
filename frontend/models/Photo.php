<?php
/**
 * Created by PhpStorm.
 * User: chiff
 * Date: 12.12.2019
 * Time: 19:10
 */

namespace frontend\models;

use yii\db\ActiveRecord;

/**
 * Class Photo
 * @package frontend\models
 * @property int $realty
 * @property string $link
 */
class Photo extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%photo}}';
    }

    public function getRealty()
    {
        return $this->hasOne(Realty::class, ['id' => 'realty']);
    }

    public static function getByPath($path)
    {
        return Photo::find()->where(['=','link',$path])->one();
    }
}