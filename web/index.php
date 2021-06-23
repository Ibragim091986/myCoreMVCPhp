<?php
require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../autoload.php';
$config = require __DIR__ . '/../config.php';


//require_once 'start.php';
//require __DIR__ . '/autoload.php';
(new vendor\classes\Core($config))->run();

//require __DIR__ . '/../start.php';

