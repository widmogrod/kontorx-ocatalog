<?php

error_reporting(E_ALL);
set_time_limit(0);

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
$application->bootstrap();

$date = new Zend_Date();

// Tworzenie indeksu
$index = new Zend_Search_Lucene(SEARCH_LUCENE_PATHNAME, true);

$catalogTable = new Catalog_Model_DbTable_Catalog();
foreach($catalogTable->fetchAll() as
			/* @var $catalogRow Zend_Db_Table_Row */ $catalogRow)
{
	// tylko rekordy opublikowane
	if (!$catalogRow->publicated)
		continue;

	// tylko rekordy wykupione
	$promoTimeTable = $catalogRow->findDependentRowset('Catalog_Model_DbTable_PromoTime');
	if (!count($promoTimeTable))
		continue;

	$promoTimeRow = $promoTimeTable->current();

	// wszystkie wizytówki, których czas promocji minie nie są indeksowane
	if ($date->isLater($promoTimeRow->t_end))
		continue;
	
	// nowy dokumnet indeksujacy
	$doc = new Zend_Search_Lucene_Document();

	
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('id', $catalogRow->id));
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('catalog_type_id', $catalogRow->id));
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('catalog_district_id', $catalogRow->catalog_district_id));
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('catalog_promo_type_id', $promoTimeRow->catalog_promo_type_id));

    $doc->addField(Zend_Search_Lucene_Field::Text('name',strip_tags($catalogRow->name)));
    $doc->addField(Zend_Search_Lucene_Field::Text('city',strip_tags($catalogRow->city)));
	$doc->addField(Zend_Search_Lucene_Field::Text('adress',strip_tags($catalogRow->adress)));
	$doc->addField(Zend_Search_Lucene_Field::Text('description',strip_tags($catalogRow->description)));

	// TODO: godziny otwarcia?
	// $timeTable = $catalogRow->findDependentRowset('Catalog_Model_DbTable_Time');

	// district
	$districtRow = $catalogRow->findParentRow('Catalog_Model_DbTable_District');
	if(count($districtRow)) {
		$doc->addField(Zend_Search_Lucene_Field::Text('district', $districtRow->name));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('district_url', $districtRow->url));
	}
	
	// image
	$imageRow = $catalogRow->findParentRow('Catalog_Model_DbTable_Image');
	if(count($imageRow)) {
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $imageRow->image));
	}
	
	// type
	$typeRow = $catalogRow->findParentRow('Catalog_Model_DbTable_Type');
	if(count($typeRow)) {
		$doc->addField(Zend_Search_Lucene_Field::Text('type',strip_tags($typeRow->name)));
	}

	// personel
	$staffTable = $catalogRow->findDependentRowset('Catalog_Model_DbTable_Staff');
	if(count($staffTable)) {
		foreach ($staffTable as $k => $staffRow) {
			$doc->addField(Zend_Search_Lucene_Field::Text('staff_name_' . $k, strip_tags($staffRow->fullname)));
		}
	}
	
	// gabinet zapewnia
	$optionsTable = $catalogRow->findManyToManyRowset('Catalog_Model_DbTable_Options','Catalog_Model_DbTable_HasOptions');
	if(count($optionsTable)) {
		foreach ($optionsTable as $k => $optionsRow) {
			// dodatkowe zapisywanie pul, trzech opcji
			// "gabinet zapewnia" w katalogu
			if ($catalogRow->catalog_option1_id == $optionsRow->id) {
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('catalog_option1_id', $optionsRow->id));
			} else
			if ($catalogRow->catalog_option2_id == $optionsRow->id) {
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('catalog_option2_id', $optionsRow->id));
			} else
			if ($catalogRow->catalog_option3_id == $optionsRow->id) {
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('catalog_option3_id', $optionsRow->id));
			}

			$doc->addField(Zend_Search_Lucene_Field::Text('options_name_' . $k, strip_tags($optionsRow->name)));
		}
	}
	
	// usługi
	$serviceTable = $catalogRow->findManyToManyRowset('Catalog_Model_DbTable_Service','Catalog_Model_DbTable_HasService');
	if(count($serviceTable)) {
		foreach ($serviceTable as $k => $serviceRow) {
			$doc->addField(Zend_Search_Lucene_Field::Text('servic_name_' . $k, strip_tags($serviceRow->name)));
		}
	}

	echo 'Create: ', $catalogRow->name, '(id:',$catalogRow->id,')',"\n";
	$index->addDocument($doc);
}