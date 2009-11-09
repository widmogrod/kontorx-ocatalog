<?php
class Page_Model_DbTable_Block extends Zend_Db_Table_Abstract  {
	protected $_name = 'page_block';
	
	protected $_referenceMap    = array(
        'Page' => array(
            'columns'           => 'page_id',
            'refTableClass'     => 'Page_Model_DbTable_Page',
            'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        ),
        'Block' => array(
            'columns'           => 'block_id',
            'refTableClass'     => 'Page_Model_DbTable_Blocks',
            'refColumns'        => 'id',
			'refColumnsAsName'  => 'name',
        	'onDelete'			=> self::CASCADE
        )
    );
}