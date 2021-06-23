<?php


namespace vendor\classes;


class AccessControl
{

    private $_config;
    private $_request;
    private $_ObjectController;

    public $only;
    public $except = [];

    public $allow;

    public $actions;

    public $controllers;



    //Правила
    public $rules = [];

    //Роль пользователя
    public $roles;

    public $permissions;


    public function __construct(Controller $ObjectController, Request $request, $config)
    {

        $this->_config = $config;
        $this->_request = $request;
        $this->_ObjectController = $ObjectController;
        $this->_viewPath = $this->getPath($this->_config['view']['viewPath']);
        $this->_layoutPath = $this->getPath($this->_config['view']['layoutPath']);
        $this->_mainFile = $this->getPath($this->_config['view']['layoutPath'], $this->_config['view']['mainFile']);


//        echo '<br>';
//        var_dump($this->_layoutPath);
//        echo '<br>';
//        var_dump($this->_request->getController());


    }

    private function setParameters()
    {
        $access = $this->_ObjectController->access();
//        echo '<br><br>';
//        var_dump($access);

    }


    /*protected function isActive($action)
    {
        $id = $this->getActionId($action);

        if (empty($this->only)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->only as $pattern) {
                if (StringHelper::matchWildcard($pattern, $id)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $pattern) {
            if (StringHelper::matchWildcard($pattern, $id)) {
                $exceptMatch = true;
                break;
            }
        }

        return !$exceptMatch && $onlyMatch;
    }*/

}