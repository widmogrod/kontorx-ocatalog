<?php
class Catalog_Model_DbTable_Type extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_type';

    protected $_dependentTables = array(
		'Catalog_Model_DbTable_Catalog'
	);
}