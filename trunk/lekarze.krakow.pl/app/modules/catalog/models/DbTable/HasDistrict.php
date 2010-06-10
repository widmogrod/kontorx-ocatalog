<?php
class Catalog_Model_DbTable_HasDistrict extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_has_district';

    protected $_referenceMap    = array(
		'Catalog' => array(
			'columns'           => 'catalog_id',
			'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
			'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        ),
		'District' => array(
			'columns'           => 'district_id',
 			'refTableClass'     => 'Catalog_Model_DbTable_District',
			'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        )
    );
}
