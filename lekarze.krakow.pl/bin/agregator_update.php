<?php

error_reporting(E_ALL);
set_time_limit(0);

/**
 * Ustawienie limitu 256MB dla 606 rekordów jest OK,
 * w przyszłości gdy będzie więcej rekordów do zaindeksowania należy...
 * 
 * TODO: Pojawiaja się problemy, bo w cyklu indeksowania prawdopodobnie cały czas
 * jest wykonywane DESCRIPTION i nie jest ono cachowane!
 */
ini_set('memory_limit', '256M');

define('BOOTSTRAP','development');
define('CATALOG_TYPE','newstomatolodzy');

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

$model = new Agregator_Model_Agregator();
$model->multiAgregate();