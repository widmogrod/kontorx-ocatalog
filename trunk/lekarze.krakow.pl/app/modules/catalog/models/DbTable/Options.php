<?php
class Catalog_Model_DbTable_Options extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_options';

    protected $_dependentTables = array(
		'Catalog_Model_DbTable_HasOptions'
    );
}