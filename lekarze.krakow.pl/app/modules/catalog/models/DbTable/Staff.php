<?php
class Catalog_Model_DbTable_Staff extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_staff';

    protected $_dependentTables = array(
        'Catalog_Model_DbTable_HasStaff'
    );

    protected $_referenceMap    = array(
        'Catalog' => array(
            'columns'           => 'catalog_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name',
            'onDelete'		=> self::CASCADE
        )
    );
}