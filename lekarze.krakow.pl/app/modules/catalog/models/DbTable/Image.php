<?php
class Catalog_Model_DbTable_Image extends KontorX_Db_Table_Abstract {
	protected $_name = 'catalog_image';

	protected $_referenceMap    = array(
        'Catalog' => array(
            'columns'           => 'catalog_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
            'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        )
    );
}