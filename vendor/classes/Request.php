<?php


namespace vendor\classes;


class Request
{

    private $_config;
    private $_controller;
    private $_action;
    private $_requestUri;
    private $_post = [];
    private $_get = [];

    public function __construct()
    {
        $this->_config = Core::$config;
        $this->setRequestUri();
        $this->setPost();

        $clearGetFromUri = explode('?', $this->getRequestUri(), 2)[0];
//        $arraUri = explode('/', $this->getRequestUri());
        $arraUri = explode('/', $clearGetFromUri);
        $this->_controller = ucwords(strtolower($arraUri[1]));
        $this->_action = ucwords(strtolower($arraUri[2]));
        if(empty($this->_action)){
            $this->_action = $this->_controller;
            $this->_controller = ucwords(strtolower($this->_config['controller']['defaultController']));
        }

    }

    public function setRequestUri()
    {
        $this->_requestUri = $_SERVER['REQUEST_URI'];
    }

    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    public function setPost()
    {
        $this->_post = $_POST;
    }

    public function getPost()
    {
        return $this->_post;
    }

    public function setGet()
    {
        $this->_post = $_GET;
    }

    public function getGet()
    {
        return $this->_get;
    }

    public function getController()
    {

        return empty($this->_controller) ? ucwords(strtolower($this->_config['controller']['defaultController'])) : $this->_controller;
    }

    public function getAction()
    {
        return empty($this->_action) ?  '' : $this->_action;
    }





}