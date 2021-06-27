<?php


namespace vendor\classes;


use vendor\interfaces\IdentityInterface;

class User
{

    public $identityClass;

    /**
     * @var IdentityInterface
     */
    public $identity = false;

    private $_config;
    private $_user;

    public function __construct()
    {
        $this->_config = Core::$config;
        $this->identityClass = $this->_config['user']['identityClass'];
        $this->setUserObject();

    }

    public function login(IdentityInterface $identity, $password, $duration = 0)
    {
        if($identity->validatePassword($password)){

            $id = $identity->getId();
            $hash = $this->generateCode();
            Cookie::find()->setName('identity')->setValue($id)->setTime($duration)->setPath('/')->create();
            Cookie::find()->setName('hash')->setValue($hash)->setTime($duration)->setPath('/')->create();
            Session::find()->set('identity', $id);
            Session::find()->set('hash', $hash);
            $this->identity = $identity;

            return true;
        }
        return false;
    }

    public function logout(){
        Session::find()->destroyAll();
        Cookie::find()->setName('identity')->delete();
        Cookie::find()->setName('hash')->delete();
        $this->identity = false;
        return true;

    }

    private function setCookieUser()
    {

        $id = $this->identityClass->id;
        $authKey = $this->identityClass->getAuthKey();

        setcookie("id", $id, time()+60*60*24*30, "/");
        setcookie("hash", $authKey, time()+60*60*24*30, "/");
    }

    private function generateCode($length=6) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";

        $code = "";

        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {

            $code .= $chars[mt_rand(0,$clen)];
        }

        return $code;

    }

    public function getIsGuest()
    {
        return $this->identity === false;
    }



    public function setUserObject()
    {

        if($this->validateCookie()){
            $identity = new $this->identityClass;
            $id = Session::find()->get('identity');
            $this->identity = $identity::findIdentity($id );

        }


        return $this;
    }

    private function validateCookie(){
        $idCookie = (string) Cookie::find()->setName('identity')->get();
        $hashCookie = (string) Cookie::find()->setName('hash')->get();
        $idSession = (string) Session::find()->get('identity');
        $hashSession = (string) Session::find()->get('hash');

        if(!empty($idSession) && !empty($hashSession)){
            return ($idCookie === $idSession) && ($hashCookie === $hashSession);
        }else{
            Cookie::find()->setName('identity')->delete();
            Cookie::find()->setName('hash')->delete();
        }

        return false;
    }

    public function getUserObject()
    {
        return empty($this->identity) ? $this->setUserObject()->identity : $this->identity;
    }

}