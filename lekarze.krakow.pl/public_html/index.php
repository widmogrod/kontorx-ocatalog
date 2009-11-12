<?php
/**
 * KontorX
 * 
 * @version 	0.1.6
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */

define('BOOTSTRAP', 'production');

defined('BOOTSTRAP')
	|| define('BOOTSTRAP',
		(getenv('BOOTSTRAP') ? getenv('BOOTSTRAP') : 'production'));

define('LIBRARY_ZEND_VERSION', '1.9.2');
define('LIBRARY_KONTORX_VERSION', 'branches/catalog');

error_reporting(0);

$paths = array('/usr/share/php/Zend/' . LIBRARY_ZEND_VERSION . '/',
    		   '/usr/share/php/KontorX/' . LIBRARY_KONTORX_VERSION . '/',
			   realpath('../app/modules'));

set_include_path(implode(PATH_SEPARATOR, $paths));

// lekarze.krakow.pl
// production
 define('G_MAP_KEY', 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRRofd7-qOKxoz0eHYyiKpxi9tWtWxR6Y2aP5q7T7h_yT-IMKv4p15b3FA');

/**
 * ReCaptacha
 */

// lekarze.krakow.pl
define('ReCAPTCHA_PUBLIC_KEY', '6LddOwgAAAAAAC380KPzvm2MkQSOUVMpMzJZbX7-');
define('ReCAPTCHA_PRIVATE_KEY', '6LddOwgAAAAAAPMbo19cuS5ZBG2OvTOHyS-6CQsV');

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend');
spl_autoload_register(array($autoloader,'autoload'));

define('APP_ENV', BOOTSTRAP);
define('APP_PATHNAME', realpath('../app'));
define('APP_MODULES_PATHNAME', APP_PATHNAME . '/modules/');
define('APP_LANGUAGE_PATHNAME', APP_PATHNAME . '/language/');

define('APP_CONFIGURATION_DIRNAME', 'configuration');
define('APP_CONFIGURATION_PATHNAME', APP_PATHNAME.'/' . APP_CONFIGURATION_DIRNAME . '/');

defined('PUBLIC_DIRNAME') or define('PUBLIC_DIRNAME', 'public_html');

define('TMP_PATHNAME', realpath('../tmp') . DIRECTORY_SEPARATOR);
define('LOG_PATHNAME', realpath('../log') . DIRECTORY_SEPARATOR);
define('PUBLIC_PATHNAME', realpath('../public_html') . DIRECTORY_SEPARATOR);


$config = new Zend_Config_Ini(APP_PATHNAME.'/configuration/application.ini', APP_ENV);
$application = new Zend_Application(
	APP_ENV,
	$config
);

try {
	$application->bootstrap();
	$application->run();
} catch (Zend_Db_Exception $e) {
	require_once 'errors/database.phtml';
} catch (Exception $e) {
	require_once 'errors/exception.phtml';
}
