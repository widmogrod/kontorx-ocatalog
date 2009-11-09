<?php
class Catalog_Model_DbTable_Service extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_service';

    protected $_dependentTables = array(
		'Catalog_Model_DbTable_HasService'
    );
}