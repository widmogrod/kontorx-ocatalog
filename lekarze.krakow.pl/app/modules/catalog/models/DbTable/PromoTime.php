<?php
class Catalog_Model_DbTable_PromoTime extends KontorX_Db_Table_Abstract {
	protected $_name = 'catalog_promo_time';
	
	protected $_referenceMap    = array(
        'PromoType' => array(
            'columns'           => 'catalog_promo_type_id',
            'refTableClass'     => 'Catalog_Model_DbTable_PromoType',
            'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        ),
        'Catalog' => array(
            'columns'           => 'catalog_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
            'refColumns'        => 'id',
			'refColumnsAsName'  => 'name'
        )
    );
}