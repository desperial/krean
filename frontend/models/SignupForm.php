<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $email;
    public $password;
    public $first_name;
    public $contact_email;
    public $contact_phone;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот адрес уже используется.'],

            ['contact_email', 'trim'],
            ['contact_email', 'required'],
            ['contact_email', 'email'],
            ['contact_email', 'string', 'max' => 255],

            ['contact_phone', 'trim'],
            ['contact_phone', 'safe'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['first_name', 'required'],
            ['first_name', 'string', 'max' => 255],


        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->contact_email = $this->contact_email;
        $user->contact_phone = $this->contact_phone;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
