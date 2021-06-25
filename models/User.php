<?php

namespace models;

use vendor\classes\Model;
use vendor\interfaces\IdentityInterface;

/* @property integer $id
 * @property string  $surname
 * @property string  $name
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

        return self::find()->findOne($id);
    }

    public function getId()
    {

    }

    public function getAuthKey()
    {

    }

    public function validateAuthKey($authKey)
    {

    }



}