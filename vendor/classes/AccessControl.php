<?php


namespace vendor\classes;


class AccessControl
{
    const GUEST                = 'guest'; // гость
    const AUTHENTICATED        = 'authenticated'; // авторизованный

    private $_config;
    private $_request;
    private $_user;
    private $_ObjectController;


    public $only;
    public $except = [];

    // public $allow;

    public $action;
    public $controller;



    //Правила
    public $rules = [];

    //Роль пользователя
    public $roles;

    public $permissions;


    public function __construct(Controller $ObjectController, $controller, $action)
    {

        $this->_config = Core::$config;
        $this->_request = Core::$request;
        $this->_user = Core::$user;
        $this->_ObjectController = $ObjectController;

        $this->controller = strtolower($controller);
        $this->action = strtolower($action);
        //$this->_viewPath = $this->getPath($this->_config['view']['viewPath']);
        //$this->_layoutPath = $this->getPath($this->_config['view']['layoutPath']);
        //$this->_mainFile = $this->getPath($this->_config['view']['layoutPath'], $this->_config['view']['mainFile']);


//        echo '<br>';
//        var_dump($this->_layoutPath);
//        echo '<br>';
//        var_dump($this->_request->getController());


    }

    private function setParameters()
    {
        $access = $this->_ObjectController->access();
        if(isset($access['only'])) $this->only = $access['only'];
        var_dump($this->only);
        if(isset($access['rules'])) $this->rules = $access['rules'];
        var_dump($this->rules);
        return $this;
//        echo '<br><br>';
//        var_dump($access);

    }

    public static function find(Controller $ObjectController, $controller, $action)
    {
        return new static($ObjectController, $controller, $action);
    }

    public static function access(Controller $ObjectController, $controller, $action)
    {

        return self::find($ObjectController, $controller, $action)
            ->setParameters()
            ->matchRules();

    }


/*
 * [
    'only' => ['login', 'logout', 'signup'],
    'rules' =>
    [
        [
        'allow' => true,
        'actions' => ['login', 'signup'],
        'roles' => ['?'],
        ],
        [
        'allow' => true,
        'actions' => ['logout'],
        'roles' => ['@'],
        ],
      ],

];*/

    public function matchRules()
    {
        if(empty($this->_ObjectController->access())) return true;

        $match = false;
        if($this->isActive()){

            foreach ($this->rules as $rule){
                $matchRules = $this->allows($rule['allow'], $rule['actions'], $this->_user, $rule['roles']);
                if($matchRules !== null){
                    $match = $match || $matchRules;
                }
            }

            return $match;
        }
        return true;

    }

    // 'allow' => true,
    public function allows($allow, $action, $user, $roles)
    {
        if ($this->matchAction($action)
            && $this->matchRole($user, $roles)
        ) {
            return $allow ? true : false;
        }

        return null;
    }


    // 'actions' => ['login', 'signup'],
    protected function matchAction($actionsRule)
    {
        return empty($actionsRule) || in_array($this->action, $actionsRule, true);
    }

    // 'roles' => ['@'],
    /**
     * @param User $user the user object
     * @return bool whether the rule applies to the role
     */
    protected function matchRole($user, $roles)
    {
        $items = empty($roles) ? [] : $roles;

        if (empty($items)) {
            return true;
        }

        if ($user === false) {
            throw new \Exception('The user application component must be available to specify roles in AccessRule.');
        }

        foreach ($items as $item) {
            if ($item === $this::GUEST) {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($item === $this::AUTHENTICATED) {
                if (!$user->getIsGuest()) {
                    return true;
                }
            }
        }

        return false;
    }

    // 'only' => ['login', 'logout', 'signup'],
    public function isActive()
    {
        if (empty($this->only)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->only as $pattern) {

                if ($pattern === $this->action) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $pattern) {
            if ($pattern === $this->action) {
                $exceptMatch = true;
                break;
            }
        }

        return !$exceptMatch && $onlyMatch;

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