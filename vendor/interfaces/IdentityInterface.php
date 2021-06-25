<?php

namespace vendor\interfaces;

interface IdentityInterface
{

    // Получить экземпляр пользователя по его идентификатору
    public static function findIdentity($id);

    // Получить идентификатор пользователя
    public function getId();

    // Этот метод возвращает ключ, используемый для основанной на cookie аутентификации.
    // Ключ сохраняется в аутентификационной cookie и позже сравнивается с версией,
    // находящейся на сервере, чтобы удостоверится, что аутентификационная cookie верная.
    public function getAuthKey();

    // Этот метод реализует логику проверки ключа для основанной на cookie аутентификации.
    public function validateAuthKey($authKey);

}