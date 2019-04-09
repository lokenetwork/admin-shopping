<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 2019/3/4
 * Time: 1:00 PM
 */

define('APPLICATION_PATH', dirname(dirname(__FILE__)));
require APPLICATION_PATH.'/vendor/autoload.php';
define('VIEW_PATH', APPLICATION_PATH.'/application/views/');
//include_once("./sphinxapi.php");
include_once(APPLICATION_PATH."/application/library/develop_test.php");
include_once(APPLICATION_PATH."/application/library/global_function.php");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors','On');
//error_reporting(E_ALL);
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application_".ini_get('yaf.environ').".ini");
define('CSS_TYPE', Yaf_Application::app()->getConfig()->css->type);
define('CSS_REL', Yaf_Application::app()->getConfig()->css->rel);

$r_db = new Medoo();
$w_db = new Medoo(['control_type' => 2]);

$application->getDispatcher()->dispatch(new Yaf_Request_Simple("CLI", "Index", "Data", "change_to_memory_engine", array("para" => 2)));

