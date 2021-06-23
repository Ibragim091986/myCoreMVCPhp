<?php

namespace models;

use vendor\classes\Model;

/* @property integer $id
 * @property string  $surname
 * @property string  $name
 * @property integer $subid
 * @property integer $schoolid
 */
class User extends Model
{

    public function tableName(){
        return 'users';
    }



}