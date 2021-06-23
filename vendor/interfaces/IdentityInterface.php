<?php


interface IdentityInterface
{

    public static function findIdentity($id);

    public function getId();

    public function getAuthKey();

    public function validateAuthKey($authKey);
}