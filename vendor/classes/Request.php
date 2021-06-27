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
    private $_host;
    private $_protocol;
    private $_homeURL;


    public function __construct()
    {
        $this->_config = Core::$config;
        $this->setRequestUri();
        $this->setPost();

        $this->explodeURI();

    }

    public function explodeURI(){
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

    public function setRequestUri($URI = null)
    {
        if($URI !== null) {
            $this->_requestUri = $URI;
        }else{
            $this->_requestUri = $_SERVER['REQUEST_URI'];
        }
        return $this;
    }

    public function getHost()
    {
        return empty($this->_host) ?  $_SERVER['HTTP_HOST'] : $this->_host;
    }

    public function getProtocol()
    {
        if(empty($this->_protocol)){
            if (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) {
                return $this->_protocol = 'https';
            } else {
                return $this->_protocol = 'http';
            }
        }
        return $this->_protocol;

    }

    public function getHomeUrl()
    {
        if(empty($this->_homeURL)){
            $protocol = $this->getProtocol();
            $host = $this->getHost();

            $controller = strtolower($this->_config['controller']['defaultController']);
            $action = strtolower($this->_config['controller']['defaultAction']);
            $url = $protocol . '://' . $host . '/' . $controller . '/' . $action;
            return $this->_homeURL = $url;
        }
        return $this->_homeURL;


    }

    public function goHome()
    {
        $this->redirect($this->getHomeUrl());
    }

    public function redirect($URL, $response_code = '0')
    {
        header("Location: " . $URL, true, $response_code);
        exit();

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
        $this->_get = $_GET;
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