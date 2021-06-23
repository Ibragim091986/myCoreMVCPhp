<?php


namespace vendor\classes;


class User
{

    public $identityClass;

    private $_config;

    public function __construct()
    {

        $this->_config = Core::$config;

    }


    private function setCookieUser()
    {

        $id = $this->identityClass->id;
        $authKey = $this->identityClass->getAuthKey();

        setcookie("id", $id, time()+60*60*24*30, "/");
        setcookie("hash", $authKey, time()+60*60*24*30, "/");
    }

    function generateCode($length=6) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";

        $code = "";

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {

            $code .= $chars[mt_rand(0,$clen)];
        }

        return $code;

    }

}