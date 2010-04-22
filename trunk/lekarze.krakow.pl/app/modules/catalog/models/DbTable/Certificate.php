<?php
class Catalog_Model_DbTable_Certificate extends KontorX_Db_Table_Abstract 
{
	protected $_name = 'catalog_certificate';
	
    protected $_referenceMap    = array(
        'Catalog' => array(
            'columns'           => 'catalog_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Catalog',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
    );
}