<?php
class Page_Model_DbTable_Page extends KontorX_Db_Table_Tree_Abstract {
	protected $_name = 'page';
	protected $_level = 'path';

	protected $_dependentTables = array(
		'Page_Model_DbTable_Block'
	);
	
	protected $_referenceMap    = array(
        'Language' => array(
            'columns'           => 'language_url',
            'refTableClass'     => 'Language',
            'refColumns'        => 'url',
			'refColumnsAsName'  => 'name',
			'onDelete'			=> self::CASCADE
        ),
        'User' => array(
            'columns'           => 'user_id',
            'refTableClass'     => 'User',
            'refColumns'        => 'id',
			'refColumnsAsName'  => 'username'
        )
    );
}