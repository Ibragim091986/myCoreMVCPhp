<?php



function my_autoloader($class) {

    include __DIR__ . '/../' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    require_once __DIR__ . '/lib/vendor/autoload.php';
}

spl_autoload_register('my_autoloader');