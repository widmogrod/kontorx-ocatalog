<?php
require_once 'KontorX/Db/Table/Tree/Abstract.php';
class CatalogPromoType extends KontorX_Db_Table_Abstract {
	protected $_name = 'catalog_promo_type';
	
	protected $_dependentTables = array(
		'CatalogPromoTime',
	);
}