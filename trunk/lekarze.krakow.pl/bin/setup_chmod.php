<?php
/**
 * Setup
 */
$basePath = dirname(dirname(__FILE__));
$appPath  = $basePath . '/app/';

$moduleConfigGlob			= $appPath . '/modules/*/*.xml';
$moduleConfigurationsGlob	= $appPath . '/modules/*/configuration/*';

// Wyszukuje plik konfiguracujny modułu
// i ustawia uprawnienia do zapisu
foreach (glob($moduleConfigGlob) as $path) {
	$result = chmod($path, 0666);

	print '[Module config]: ' . $path . "\n";
	printf("\t chmod: %s \n", $result ? 'yes' : 'no');
}

// Wyszukuje wszystkie pliki konfiguracujne modułów
// i zmienia ich uprawnienia
foreach (glob($moduleConfigurationsGlob) as $path) {
	$result = chmod($path, 0666);

	print '[Module configurations]: ' . $path . "\n";
	printf("\t chmod: %s \n", $result ? 'yes' : 'no');
}