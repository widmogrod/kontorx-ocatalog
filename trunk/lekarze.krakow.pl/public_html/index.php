<?php
// globalne ustawienia aplikacji
require_once '../app/configuration/core.php';

// biblioteki
set_include_path(implode(PATH_SEPARATOR, array(
	'/usr/share/php/Zend/' . LIBRARY_ZEND_VERSION . '/',
	'/usr/share/php/KontorX/' . LIBRARY_KONTORX_VERSION . '/',
	APP_MODULES_PATHNAME,
	LIB_PATHNAME)));

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

try {
	// uruchamiam aplikację
	$application->bootstrap();
	$application->run();
} catch (Zend_Db_Exception $e) {
	require_once 'errors/database.phtml';
} catch (Exception $e) {
	require_once 'errors/exception.phtml';
}
