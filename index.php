<?php
define('APPLICATION_PATH', dirname(__FILE__));
error_reporting (E_ALL ^ E_NOTICE );
ini_set( 'display_errors', 'On' );
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");
$application->getDispatcher()->catchException(true);
$application->bootstrap()->run();
?>
