<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * ContactForm is the model behind the contact form.
 */
class Realty extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return '{{%overhill_realty}}';
    }

    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['realty' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

}

class Photo extends ActiveRecord
{ 
	const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
   public static function tableName()
    {
        return '{{%overhill_realty_photos}}';
    }

    public function getRealty()
    {
        return $this->hasOne(Realty::className(), ['id' => 'realty']);
    }
}

class User extends ActiveRecord
{ 
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
   public static function tableName()
    {
        return '{{%overhill_users}}';
    }

    public function getRealty()
    {
        return $this->hasMany(Realty::className(), ['user' => 'id']);
    }
}