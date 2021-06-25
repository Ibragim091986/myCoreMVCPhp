<?php


namespace vendor\classes;


class Session
{
    private $cookieTime;


    // задаем время жизни сессионных кук
    public function __construct(string $cookieTime = '+30 days') {
        $this->cookieTime = strtotime($cookieTime);
        session_cache_limiter(false);
        $this->start();
    }


    // стартуем сессию
    public function start()
    {
        session_start();
    }

    // закрывает сессию
    public function close()
    {
        session_write_close();
    }


    /**
     * Проверяем сессию на наличие в ней переменной c заданным именем
     */
    public function has($name)
    {
        return isset($_SESSION[$name]);
    }



    /**
     * Устанавливаем сессию с именем $name и значением $value
     *
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }



    /**
     * Когда мы хотим сохранить в сессии сразу много значений - используем массив
     *
     * @param $vars
     */
    public function setArray(array $vars)
    {
        foreach($vars as $name => $value) {
            $this->set($name, $value);
        }
    }



    /**
     * Получаем значение сессий
     *
     * @param $name
     * @return mixed
     */
    public function get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $_SESSION[$name];
    }

    public function getSessionName()
    {
        return session_name();

    }



    /**
     * @param $name - Уничтожаем сессию с именем $name
     */
    public function destroy($name) {
        unset($_SESSION[$name]);
    }



    /**
     * Полностью очищаем все данные пользователя
     */
    public function destroyAll() {
        session_destroy();
    }

    /**
     * Удалаяем кукки сессии
     * @param $name
     */
    public function removeCookie() {
        setcookie(session_name(), null);
    }


}