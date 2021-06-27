<?php

namespace models;

use vendor\classes\Model;
use vendor\interfaces\IdentityInterface;

/* @property integer $id
 * @property string  $surname
 * @property string  $name
 * @property string  $password_hash
 * @property integer $subid
 * @property integer $schoolid
 */
class User extends Model implements IdentityInterface
{

    public function tableName(){
        return 'users';
    }

    public static function findIdentity($id)
    {
        return static::find()->findOne($id);
    }

    public function validatePassword($password)
    {
        return md5($password) === $this->password_hash;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public static function findByUsername($username)
    {

        static::find()->findOne();
        return static::findOne(['username' => $username]);
    }



}