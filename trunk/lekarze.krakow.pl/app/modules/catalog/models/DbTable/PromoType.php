<?php
class Catalog_Model_DbTable_PromoType extends KontorX_Db_Table_Abstract {
	protected $_name = 'catalog_promo_type';
	
	protected $_dependentTables = array(
		'Catalog_Model_DbTable_PromoTime',
	);
}