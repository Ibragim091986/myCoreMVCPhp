<?php


namespace vendor\classes;


class Controller
{

    public $defaultAction = '';

    private $_config;
    private $_request;
    private $_path;
    private $_view;

    public function __construct()
    {

        $this->_config = Core::$config;
        $this->_request = Core::$request;
        $this->setPath($this->_config['controller']['controllerPath']);
        $this->defaultAction = empty($this->defaultAction) ? $this->_config['controller']['defaultAction'] : $this->defaultAction;

    }


    public static function run()
    {

        $controllerBase =  new static();

        $controllerBase->getPatchControllerAction($file, $ObjectController, $MethodAction, $controller, $action);


//        var_dump($ObjectController);

        $controllerBase->runAction($ObjectController, $MethodAction);
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
        $path = '..' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;

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

            $controllerPath = '\controllers\\' . $controller .'Controller';
            $ObjectController = new $controllerPath($this->_request, $this->_config);

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


    public function runAction($ObjectController, $MethodAction)
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

        //Установка экшена по умолчанию
        $this->defaultAction = $ObjectController->defaultAction;
        call_user_func_array([$ObjectController, $MethodAction], $methodParams);
    }

    public function render($view = 'index', $arraValue = [], $renderMain = true)
    {
        $content = $this->getView()->render($view,  $arraValue);
        return $renderMain ? $this->renderContent($content): $content;

    }

    public function renderContent($content)
    {


        $layoutFile = $this->getView()->getMainFile();

        if ($layoutFile !== false) {
            return $this->getView()->renderFile($layoutFile, ['content' => $content]);
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