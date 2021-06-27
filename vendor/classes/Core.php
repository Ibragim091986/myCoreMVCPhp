<?php

namespace vendor\classes;

class Core
{
    private $_config;
    private $_request;
    private $_user;


    public static $config;

    /**
     * @var Request
     */
    public static $request;

    /**
     * @var User
     */
    public static $user;
    public static $homeDir;

    /**
     * @var Session
     */
    public static $session;

    public static $microTimeStart;


    public function __construct($config)
    {
        $this->_config = static::$config = $config;
        $this->_request = static::$request = new Request();
        $this->_user = static::$user = new User();
        static::$homeDir = __DIR__ . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR;
        static::$session = new Session();
        static::$microTimeStart = microtime(true);


    }

    public function run(){


//        $requestUri = $_SERVER['REQUEST_URI'];
//        var_dump($requestUri[0]);
//        $requestUri = preg_replace('/^(http|https|):\/\/[^\/]+/i', '', $requestUri);
//        $requestUri = explode('/', $requestUri);
//        echo 'RUN CORE ' . var_dump($_SERVER['REQUEST_URI']);
//        var_dump($this->_request->getController());
//        var_dump($this->_request->getAction());

//        $control = new Controller($this->_request, $this->_config);

//        $Model = new Model();
//        $start = microtime(true);
        Controller::run();


//        var_dump($control);
//        echo '<br>RUN CORE 2 ';


    }


    function delegate(){

    }

}