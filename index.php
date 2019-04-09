<?php
require 'vendor/autoload.php';
define('APPLICATION_PATH', dirname(__FILE__));
define('VIEW_PATH', APPLICATION_PATH.'/application/views/');
//include_once("./sphinxapi.php");
ini_set('date.timezone','Asia/Shanghai');
date_default_timezone_set('Asia/Shanghai');
include_once(APPLICATION_PATH."/application/library/develop_test.php");
include_once(APPLICATION_PATH."/application/library/global_function.php");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application_".ini_get('yaf.environ').".ini");
define('CSS_TYPE', Yaf_Application::app()->getConfig()->css->type);
define('CSS_REL', Yaf_Application::app()->getConfig()->css->rel);
if(!isset($_SESSION)){
	session_start();
}
$r_db = new Medoo();
$w_db = new Medoo(['control_type' => 2]);


$application->getDispatcher()->throwException(false);
$application->bootstrap()->run();


?>
