<?php
$config =[
    'view' =>[
        'viewPath' => 'views',
        'layoutPath' => 'views/layouts',
        'mainFile' => 'main',
    ],
    'controller' =>[
        'controllerPath' => 'controllers',
        'defaultController' => 'home',
        'defaultAction' => 'index',
    ],
    /*'controllerPath' => 'controllers',*/
    'db' => [
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ],
];

return $config;