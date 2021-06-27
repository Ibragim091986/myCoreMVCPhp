<?php

namespace vendor\classes;

class View
{

    private $_config;
    private $_request;
    private $_layoutPath;
    private $_viewPath;
    private $_mainFile;
    private $_viewFiles;



    public function __construct()
    {

        $this->_config = Core::$config;
        $this->_request = Core::$request;
        $this->_viewPath = $this->getPath($this->_config['view']['viewPath']);
        $this->_layoutPath = $this->getPath($this->_config['view']['layoutPath']);
        $this->_mainFile = $this->getPath($this->_config['view']['layoutPath'], $this->_config['view']['mainFile']);


//        echo '<br>';
//        var_dump($this->_layoutPath);
//        echo '<br>';
//        var_dump($this->_request->getController());


    }


    public function getPath($path, $file = false)
    {

        $path = trim($path, './\\');
//        $path = trim($path, '.\\');
        $pathArray = explode('/', $path);

        $path = '..';

        foreach ($pathArray as $key => $value){
            $path .= DIRECTORY_SEPARATOR . $value;
        }

        $path .= DIRECTORY_SEPARATOR;

        if (is_dir($path) == false) {

            throw new \Exception ('Не верный путь для вьювера: `' . $path . '`');

        }

        if($file && !is_file($path . $file . '.php')){

            throw new \Exception ('Не могу найти файл: `' . $path . $file . '`');

        }

        if($file !== false){
            return  $path . $file . '.php';
        }

        return  $path;

    }


    public function render($view, $params = [])
    {

        $viewFile = $this->findViewFile($view);
        return $this->renderFile($viewFile, $params);
    }

    private function findViewFile($view)
    {
        $controller = $this->_request->getController();
        $action = $this->_request->getAction();


        if(empty($action) && !empty($controller)){
            $action = $controller;
            $controller = $this->_config['controller']['defaultController'];
        }

        $file = $this->getPath($this->_viewPath . strtolower($controller), $view);
        /*$file = $this->_viewPath . strtolower($this->_request->getController()) . DIRECTORY_SEPARATOR . $view . '.php' ;
        if(!is_file($file)){
            throw new \Exception ('Не могу найти файл: `' . $file . '`');
        }*/
        $this->_viewFiles = $file;
        return $file;
    }

    public function renderFile($viewFile, $params)
    {

        $output = $this->renderPhpFile($viewFile, $params);
        return  $output;
    }

    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }

    }

    public function getMainFile()
    {
        return $this->_mainFile;
    }

    public function setMainFile($mainFile)
    {
        $this->_mainFile = $mainFile;
    }
//
//    protected function findViewFile($view, $context = null)
//    {
//        if (strncmp($view, '@', 1) === 0) {
//            // e.g. "@app/views/main"
//            $file = Yii::getAlias($view);
//        } elseif (strncmp($view, '//', 2) === 0) {
//            // e.g. "//layouts/main"
//            $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
//        } elseif (strncmp($view, '/', 1) === 0) {
//            // e.g. "/site/index"
//            if (Yii::$app->controller !== null) {
//                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
//            } else {
//                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
//            }
//        } elseif ($context instanceof ViewContextInterface) {
//            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
//        } elseif (($currentViewFile = $this->getRequestedViewFile()) !== false) {
//            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
//        } else {
//            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
//        }
//
//        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
//            return $file;
//        }
//        $path = $file . '.' . $this->defaultExtension;
//        if ($this->defaultExtension !== 'php' && !is_file($path)) {
//            $path = $file . '.php';
//        }
//
//        return $path;
//    }

//    public function renderFile($viewFile, $params = [], $context = null)
//    {
//        $viewFile = $requestedFile = Yii::getAlias($viewFile);
//
//        if ($this->theme !== null) {
//            $viewFile = $this->theme->applyTo($viewFile);
//        }
//        if (is_file($viewFile)) {
//            $viewFile = FileHelper::localize($viewFile);
//        } else {
//            throw new ViewNotFoundException("The view file does not exist: $viewFile");
//        }
//
//        $oldContext = $this->context;
//        if ($context !== null) {
//            $this->context = $context;
//        }
//        $output = '';
//        $this->_viewFiles[] = [
//            'resolved' => $viewFile,
//            'requested' => $requestedFile
//        ];
//
//        if ($this->beforeRender($viewFile, $params)) {
//            Yii::debug("Rendering view file: $viewFile", __METHOD__);
//            $ext = pathinfo($viewFile, PATHINFO_EXTENSION);
//            if (isset($this->renderers[$ext])) {
//                if (is_array($this->renderers[$ext]) || is_string($this->renderers[$ext])) {
//                    $this->renderers[$ext] = Yii::createObject($this->renderers[$ext]);
//                }
//                /* @var $renderer ViewRenderer */
//                $renderer = $this->renderers[$ext];
//                $output = $renderer->render($this, $viewFile, $params);
//            } else {
//                $output = $this->renderPhpFile($viewFile, $params);
//            }
//            $this->afterRender($viewFile, $params, $output);
//        }
//
//        array_pop($this->_viewFiles);
//        $this->context = $oldContext;
//
//        return $output;
//    }
}