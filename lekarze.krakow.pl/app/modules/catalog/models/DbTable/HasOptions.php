<?php
class Catalog_Model_DbTable_HasOptions extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_has_catalog_options';

    protected $_referenceMap    = array(
		'Catalog' => array(
			'columns'           => 'catalog_id',
			'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
			'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        ),
		'CatalogOptions' => array(
			'columns'           => 'catalog_options_id',
 			'refTableClass'     => 'Catalog_Model_DbTable_Options',
			'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        )
    );
}