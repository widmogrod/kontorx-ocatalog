<?php
class Catalog_Model_DbTable_District extends KontorX_Db_Table_Tree_Abstract {
    protected $_name = 'catalog_district';
    protected $_level = 'path';
    
    protected $_dependentTables = array(
        'Catalog_Model_DbTable_Catalog'
    );
}