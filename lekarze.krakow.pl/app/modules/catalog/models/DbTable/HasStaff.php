<?php
class Catalog_Model_DbTable_HasStaff extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_has_catalog_staff';

    protected $_referenceMap    = array(
        'Catalog' => array(
            'columns'           => 'catalog_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name',
            'onDelete'		=> self::CASCADE
        ),
        'Staff' => array(
            'columns'           => 'catalog_staff_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Staff',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'fullname',
            'onDelete'		=> self::CASCADE
        ),
    );
}