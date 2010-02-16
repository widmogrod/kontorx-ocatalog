<?php
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '256M');

define('BOOTSTRAP','development');
define('CATALOG_TYPE','newlekarze');

// globalne ustawienia aplikacji
require_once '../app/configuration/core.php';

// biblioteki
set_include_path(implode(PATH_SEPARATOR, array(
	'/usr/share/php/Zend/' . LIBRARY_ZEND_VERSION . '/',
	'/usr/share/php/KontorX/' . LIBRARY_KONTORX_VERSION . '/',
	APP_MODULES_PATHNAME)));

// autoloader
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend');
spl_autoload_register(array($autoloader,'autoload'));

// przygotuj aplikacje
$config = new Zend_Config_Ini(APP_PATHNAME . '/configuration/application.ini', BOOTSTRAP);
$application = new Zend_Application(
	BOOTSTRAP,
	$config
);

// uruchamiam aplikację
$application->getBootstrap()->bootstrap();

$data = include 'data.php';
//var_dump($data);
$table = new Catalog_Model_DbTable_Catalog();
//$table->insert();

foreach ($data as $row)
{
//	print_r($row);
	$row['city'] = 'Kraków';
	unset($row['district']);
	$row['adress'] = $row['address'];
	unset($row['address']);

	// apteka ID: 38
	// 
	$row['catalog_type_id'] = 38;
	$row['publicated'] = 0;
	
	try {
		$table->insert($row);
	} catch(Zend_Db_Exception $e) {
		echo 'Exception: ', $e->getMessage(), "\n";
	}
}