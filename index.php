<?php
error_reporting(E_ALL & ~E_NOTICE);
require 'Core/defines.php';
require 'Core/RestServer.php';
require 'Controller/TestController.php';
//require 'Controller/EntrepriseController.php';
//require 'Controller/FileController.php';

$server = new RestServer('debug');
$server->addClass('TestController');
//$server->addClass('EntrepriseController');
//$server->addClass('FileController');
$server->handle();
