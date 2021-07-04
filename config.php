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
    'user' => [
        'identityClass' => 'models\User',
        'enableAutoLogin' => true,
        'loginUrl' => '/home/login',
        'errorAction' => '/home/error',
    ],
    /*'controllerPath' => 'controllers',*/
    'db' => [
        'host' => 'localhost',
        'dbname' => 'test',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ],
    'amocrm' => [
        'clientId' => '37921d55-3e69-4cfe-b395-010dd787ca1e',
        'clientSecret' => 'hU7OaFvZzEdXNzZfGFgQ1vKRnrjvsTK4fTco7vlHXKacUC7E8tENgscDsyk65qxR',
        'redirectUri' => 'http://fa09d9bf3859.ngrok.io/amocrm',
    ],
];

return $config;