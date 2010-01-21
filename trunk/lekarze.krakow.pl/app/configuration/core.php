<?php
/**
 * Podstawowa konfiguracja aplikacji 
 */

defined('BOOTSTRAP')
	|| define('BOOTSTRAP',
		(getenv('BOOTSTRAP') ? getenv('BOOTSTRAP') : 'production'));

defined('CATALOG_TYPE')
	|| define('CATALOG_TYPE',
		(getenv('CATALOG_TYPE') ? getenv('CATALOG_TYPE') : 'newlekarze'));


/**
 * Jakie (główne) wersje biblioteki będą uzywane 
 */
define('LIBRARY_ZEND_VERSION', '1.9.2');
define('LIBRARY_KONTORX_VERSION', 'branches/catalog');

define('APP_ENV', BOOTSTRAP);
define('APP_PATHNAME', realpath('../app'));
define('APP_MODULES_PATHNAME', APP_PATHNAME . '/modules/');
define('APP_LANGUAGE_PATHNAME', APP_PATHNAME . '/language/');

define('APP_CONFIGURATION_DIRNAME', 'configuration');
define('APP_CONFIGURATION_PATHNAME', APP_PATHNAME.'/' . APP_CONFIGURATION_DIRNAME . '/');

defined('PUBLIC_DIRNAME') or define('PUBLIC_DIRNAME', 'public_html');

define('TMP_PATHNAME', realpath('../tmp') . DIRECTORY_SEPARATOR);
define('SEARCH_LUCENE_PATHNAME', TMP_PATHNAME . DIRECTORY_SEPARATOR . 'search' . DIRECTORY_SEPARATOR);
define('LOG_PATHNAME', realpath('../log') . DIRECTORY_SEPARATOR);
define('PUBLIC_PATHNAME', realpath('../' . PUBLIC_DIRNAME) . DIRECTORY_SEPARATOR);

/**
 * Ustawienia katalogow 
 */
$catalogOptions = array(
	'newlekarze' => array(
		'production' => array(
			'CATALOG_HOSTNAME'  => 'lekarze.krakow.pl',
			'CATALOG_SHOW_ROUTE'  => 'lekarz',
			'CATALOG_LIST_ROUTE'  => 'lekarze',
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRRofd7-qOKxoz0eHYyiKpxi9tWtWxR6Y2aP5q7T7h_yT-IMKv4p15b3FA',
			'ReCAPTCHA_PUBLIC_KEY' => '6LddOwgAAAAAAC380KPzvm2MkQSOUVMpMzJZbX7-',
			'ReCAPTCHA_PRIVATE_KEY' => '6LddOwgAAAAAAPMbo19cuS5ZBG2OvTOHyS-6CQsV'
		),
		'development' => array(
			// http://lekarze.krakow.lh
			'CATALOG_HOSTNAME'  => 'lekarze.krakow.lh',
			'CATALOG_SHOW_ROUTE'  => 'lekarz',
			'CATALOG_LIST_ROUTE'  => 'lekarze',
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRRy31BfvwgWWIRKeGy_HOv1ENUTOhTmnFdE9LntmoLBQlfpJ93rZqvJRQ',
			'ReCAPTCHA_PUBLIC_KEY' => '6LcWwAkAAAAAAJBSCOr3ctqYHEy2PLJxP2a2OFEe',
			'ReCAPTCHA_PRIVATE_KEY' => '6LcWwAkAAAAAANzH-UDrJtbyuVHBo6Q7sAp94C1u'
		)
	),
	'newstomatolodzy' => array(
		'production' => array(
			'CATALOG_HOSTNAME'  => 'stomatolodzy.krakow.pl',
			'CATALOG_SHOW_ROUTE'  => 'stomatolog',
			'CATALOG_LIST_ROUTE'  => 'stomatolodzy',
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRScd4uuoniCqHZk8TRh2rI1gQbdrBSg5tTOx4YcGCXRk0Q_bCKoUGwr2Q',
			'ReCAPTCHA_PUBLIC_KEY' => '6Ld8MggAAAAAAGnhJiTdAEaylMic50irKkVjS77B',
			'ReCAPTCHA_PRIVATE_KEY' => '6Ld8MggAAAAAAGnhJiTdAEaylMic50irKkVjS77B'
		),
		'development' => array(
			// http://stomatolodzy.krakow.lh
			'CATALOG_HOSTNAME'  => 'stomatolodzy.krakow.lh',
			'CATALOG_SHOW_ROUTE'  => 'stomatolog',
			'CATALOG_LIST_ROUTE'  => 'stomatolodzy',
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRQldNxPkYdYxKRCRmM_80kVTahxvRQdvL1bkbtpmSHPq0D_TlqM332WCg',
			'ReCAPTCHA_PUBLIC_KEY' => '',
			'ReCAPTCHA_PRIVATE_KEY' => ''
		)
	),
	'newweterynarze' => array(
		'production' => array(
			// http://weterynarze.krakow.pl
			'CATALOG_HOSTNAME'  => 'weterynarze.krakow.pl',
			'CATALOG_SHOW_ROUTE'  => 'weterynarz',
			'CATALOG_LIST_ROUTE'  => 'weterynarze',
			'G_MAP_KEY' => '',
			'ReCAPTCHA_PUBLIC_KEY' => '',
			'ReCAPTCHA_PRIVATE_KEY' => ''
		),
		'development' => array(
			// http://weterynarze.krakow.lh
			'CATALOG_HOSTNAME'  => 'weterynarze.krakow.lh',
			'CATALOG_SHOW_ROUTE'  => 'weterynarz',
			'CATALOG_LIST_ROUTE'  => 'weterynarze',
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRS3zPBMZjraUIlNdh4fHyQGaVbhMBTmNckxgki84KmNaN9kka5-ii5QAA',
			'ReCAPTCHA_PUBLIC_KEY' => '',
			'ReCAPTCHA_PRIVATE_KEY' => ''
		)
	),
	'newfryzjerzy' => array(
		'production' => array(
			// http://fryzjerzy.krakow.pl
			'CATALOG_HOSTNAME'  => 'fryzjerzy.krakow.pl',
			'CATALOG_SHOW_ROUTE'  => 'fryzjer',
			'CATALOG_LIST_ROUTE'  => 'fryzjerzy',
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRTH_EYAJJ2FGozMHeu-WPl9oFALgxQbBw55RXMjxFJlLubWzB8QbxoO-g',
			'ReCAPTCHA_PUBLIC_KEY' => '6LcN1gkAAAAAAPYkkT9qraj-vpeQj4VTcq1Gd4ls',
			'ReCAPTCHA_PRIVATE_KEY' => '6LcN1gkAAAAAABddwk3uB2y7mPbgsou0sWJOU3EZ'
		),
		'development' => array(
			// http://fryzjerzy.krakow.lh
			'CATALOG_HOSTNAME'  => 'lekarze.krakow.lh',
			'CATALOG_SHOW_ROUTE'  => 'fryzjer',
			'CATALOG_LIST_ROUTE'  => 'fryzjerzy',
			// dane na lekarze.krakow.pl
			'G_MAP_KEY' => 'ABQIAAAAnCqO9l1WMOgTCJlg9kVlMRRy31BfvwgWWIRKeGy_HOv1ENUTOhTmnFdE9LntmoLBQlfpJ93rZqvJRQ',
			'ReCAPTCHA_PUBLIC_KEY' => '6LcWwAkAAAAAAJBSCOr3ctqYHEy2PLJxP2a2OFEe',
			'ReCAPTCHA_PRIVATE_KEY' => '6LcWwAkAAAAAANzH-UDrJtbyuVHBo6Q7sAp94C1u'
		)
	)
);

/**
 * Ustawianie globalnej konfiguracji w zalrzeności od rodzaju katalogu 
 */
if (!isset($catalogOptions[CATALOG_TYPE])) {
	// informujemy że coś jest nie tak!
	die(sprintf('Przepraszamy, trwają prace konserwacyjne nad serwisem. Wracamy niebawem [%s,%s]', CATALOG_TYPE, BOOTSTRAP));
}

// dziedziczenie konfiguracji testing z production!
if (BOOTSTRAP == 'testing') {
	$catalogOptions[CATALOG_TYPE]['testing'] = $catalogOptions[CATALOG_TYPE]['production'];
}

if (isset($catalogOptions[CATALOG_TYPE][BOOTSTRAP])) {
	// ustawienia zostały znalezione
	$catalogOptions = $catalogOptions[CATALOG_TYPE][BOOTSTRAP];

	// definiowanie nazwy strony internetowej (wykorzystuje router.ini)
	defined('CATALOG_HOSTNAME')
		|| define('CATALOG_HOSTNAME', $catalogOptions['CATALOG_HOSTNAME']);

	// definiowanie nazwy dla router.ini i konfiguracji JS
	// pokaż wizytówkę "catalog-show"
	defined('CATALOG_SHOW_ROUTE')
		|| define('CATALOG_SHOW_ROUTE', $catalogOptions['CATALOG_SHOW_ROUTE']);
	// pokaz listę/spis "catalog-category"
	defined('CATALOG_LIST_ROUTE')
		|| define('CATALOG_LIST_ROUTE', $catalogOptions['CATALOG_LIST_ROUTE']);
	
		
		
	// API key dla GMaps
	defined('G_MAP_KEY')
		|| define('G_MAP_KEY', $catalogOptions['G_MAP_KEY']);

	// API keys dla ReCaptacha
	defined('ReCAPTCHA_PUBLIC_KEY')
		|| define('ReCAPTCHA_PUBLIC_KEY', $catalogOptions['ReCAPTCHA_PUBLIC_KEY']);
	defined('ReCAPTCHA_PRIVATE_KEY')
		|| define('ReCAPTCHA_PRIVATE_KEY', $catalogOptions['ReCAPTCHA_PRIVATE_KEY']);

	// czyszczę zbędne rzeczy
	unset($catalogOptions);
} else {
	// informujemy że coś jest nie tak!
	die(sprintf('Przepraszamy, trwają prace konserwacyjne nad serwisem. Wracamy niebawem [%s,%s] !', CATALOG_TYPE, BOOTSTRAP));
}