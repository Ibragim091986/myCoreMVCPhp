<?php


namespace vendor\classes;


use http\Env\Response;
use Ramsey\Uuid\Type\Hexadecimal;

class Controller
{

    public $defaultAction = '';

    private $_config;
    private $_request;
    private $_path;
    private $_view;
    private $_user;

    public function __construct()
    {

        $this->_config = Core::$config;
        $this->_request = Core::$request;
        $this->_user = Core::$user;
        $this->setPath($this->_config['controller']['controllerPath']);
        $this->defaultAction = empty($this->defaultAction) ? ucwords(strtolower($this->_config['controller']['defaultAction'])) : $this->defaultAction;

    }


    public static function run()
    {

        $controllerBase =  new static();

        $controllerBase->getPatchControllerAction($file, $ObjectController, $MethodAction, $controller, $action);


//        var_dump($ObjectController);

        $controllerBase->runAction($ObjectController, $MethodAction, $controller, $action);
//        $ObjectController->$MethodAction();

//        var_dump($file);
//        echo '<br>';
//        var_dump($ObjectController);
//        echo '<br>';
//        var_dump($MethodAction);
//        echo '<br>';
//        var_dump($controller);
//        echo '<br>';
//        var_dump($action);
//        echo '<br>';
//        return '';
    }


    function setPath($path)
    {

        $path = trim($path, '/\\');
        $path = Core::$homeDir . $path . DIRECTORY_SEPARATOR;

        if (is_dir($path) == false) {

            throw new \Exception ('Не верный путь для контроллеров: `' . $path . '`');

        }

        $this->_path = $path;

    }

    private function getPatchControllerAction(&$file, &$ObjectController, &$MethodAction, &$controller, &$action)
    {

        $controller = $this->_request->getController();

//        $action = empty($this->_request->getAction()) ? ucwords(strtolower($this->defaultAction)) :  $this->_request->getAction();
        $fullpath = $this->_path . $controller;
        $file = $fullpath . 'Controller.php';
//        $MethodAction = 'action' . $action;

        if (is_file($file)) {

            $controllerFolder = $this->_config['controller']['controllerPath'];
            $controllerFolder = empty($controllerFolder) ? 'controllers' : $controllerFolder;

            $controllerPath = '\\' .$controllerFolder. '\\' . $controller .'Controller';
            $ObjectController = new $controllerPath();

            $action = empty($this->_request->getAction()) ? ucwords(strtolower($ObjectController->defaultAction)) :  $this->_request->getAction();
            $MethodAction = 'action' . $action;

            if(method_exists( $ObjectController, $MethodAction )){

                //ТЕСТ ЗАПУСК
//                $ObjectController->actionIndex();
            }else{
                die ('404 Not Found (не верный экшен) ' . $action);
            }


        }else{
            die ('404 Not Found (не верный контроллер) ' . $controller);
        }

    }


    private function runAction($ObjectController, $MethodAction, $controller, $action)
    {

        $method = new \ReflectionMethod($ObjectController, $MethodAction);
        $params = $_GET;
//        $params = ['id' => '0000', 'tabu' => ''];
        $methodParams = [];


        foreach ($method->getParameters() as $param){
            //пареметр из GET запроса Action
            $name = $param->getName();


            if (array_key_exists($name, $params)){
                $methodParams[$name] = $params[$name];

            }else{
                try {
                if(!$param->isDefaultValueAvailable()){
                    throw new \Exception('Не указана обязательная переменная ' . $name);
                }
                    $methodParams[$name] = $param->getDefaultValue();
                }catch (\Exception $e) {
                    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
                    die ();
                }
            }
        }

        //Установка экшена по умолчанию из объекта $ObjectController, если он задан в нем
        $this->defaultAction = $ObjectController->defaultAction;

        /*
        проверка правил (rule), вытаскивая параметры из $ObjectController

        */

        if(AccessControl::access($ObjectController, $controller, $action)){
            call_user_func_array([$ObjectController, $MethodAction], $methodParams);
        }elseif(!$this->redirectOnError($ObjectController, $controller, $this->_config['user']['loginUrl'], '0')){
            $this->redirectOnError($ObjectController, $controller, $this->_config['user']['errorAction'], '0');
        }



    }

    private function redirectOnError($ObjectController, $controller, $redirectURI, $response_code = 0)
    {

        $this->_request->setRequestUri($redirectURI)->explodeURI();
        $action = $this->_request->getAction();
        if(AccessControl::access($ObjectController, $controller, $action)){

            $protocol = $this->_request->getProtocol();
            $host = $this->_request->getHost();
            $controller = strtolower($this->_request->getController());
            $action = strtolower($this->_request->getAction());
            $url = $protocol . '://' . $host . '/' . $controller . '/' . $action;
            $this->_request->redirect($url, $response_code);
            //header("Location: " . $url, true, $response_code);
            //exit();
            //self::run();
            //return true;

        }else{
            return false;
        }

    }

    // $mainLayout = 'views/layouts/liginmain'
    public function render($view = 'index', $arraValue = [], $layoutPath = null, $mainFile = null, $renderMain = true)
    {
        $content = $this->getView()->render($view,  $arraValue);

        return $renderMain ? $this->renderContent($content, $arraValue, $layoutPath, $mainFile): $content;

    }

    public function renderContent($content,$arraValue = [], $layoutPath = null, $mainFile = null)
    {
        $matchPath = false;
        if(($layoutPath !== null) || ($mainFile !== null) ) $matchPath = true;

        if($matchPath && ($layoutPath === null)){
            $layoutPath = $this->_config['view']['layoutPath'];
        }
        if($matchPath && ($mainFile === null)){
            $mainFile = $this->_config['view']['mainFile'];
        }

        if($matchPath){
            $path =  $this->getView()->getPath($layoutPath, $mainFile);
            $this->getView()->setMainFile($path);
        }

        $layoutFile = $this->getView()->getMainFile();

        if ($layoutFile !== false) {
            $arraValue = ['content' => $content] + $arraValue;
            return $this->getView()->renderFile($layoutFile, $arraValue);
        }

        return $content;
    }

    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = new View();
        }

        return $this->_view;
    }

    public function access(){
        return [];
    }


}