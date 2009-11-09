<?php
class Catalog_Model_DbTable_HasService extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_service_cost';

    protected $_referenceMap    = array(
		'Catalog' => array(
			'columns'           => 'catalog_id',
			'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
			'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        ),
		'CatalogOptions' => array(
			'columns'           => 'catalog_service_id',
 			'refTableClass'     => 'Catalog_Model_DbTable_Service',
			'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        )
    );
}