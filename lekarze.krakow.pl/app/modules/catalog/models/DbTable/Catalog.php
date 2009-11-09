<?php
class Catalog_Model_DbTable_Catalog extends KontorX_Db_Table_Abstract {
	protected $_name = 'catalog';
	
	protected $_dependentTables = array(
        'Catalog_Model_DbTable_HasService',
        'Catalog_Model_DbTable_PromoTime',

		'Catalog_Model_DbTable_Image',
        'Catalog_Model_DbTable_Staff',
        'Catalog_Model_DbTable_HasStaff',
		'Catalog_Model_DbTable_HasOptions',
		'Catalog_Model_DbTable_Site',
		'Catalog_Model_DbTable_Time'
    );

    protected $_referenceMap    = array(
        'District' => array(
            'columns'           => 'catalog_district_id',
            'refTableClass'     => 'Catalog_Model_DbTable_District',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name',
            'onDelete'		=> self::CASCADE
        ),
        'Image' => array(
            'columns'           => 'catalog_image_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Image',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'image'
        ),
        'Type' => array(
            'columns'           => 'catalog_type_id',
            'refTableClass'     => 'Catalog_Model_DbTable_Type',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name',
            'onDelete'          => self::CASCADE
        ),
        'Option1' => array(
            'columns'           => 'catalog_option1_id',
            'refTableClass'     => 'Catalog_Model_DbTable_HasOptions',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
        'Option2' => array(
            'columns'           => 'catalog_option2_id',
            'refTableClass'     => 'Catalog_Model_DbTable_HasOptions',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
        'Option3' => array(
            'columns'           => 'catalog_option3_id',
            'refTableClass'     => 'Catalog_Model_DbTable_HasOptions',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
        'User' => array(
            'columns'           => 'user_id',
            'refTableClass'     => 'User',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'username'
        )
    );
}