<?php
class Page_Model_DbTable_Blocks extends Zend_Db_Table_Abstract  {
	protected $_name = 'page_blocks';

	protected $_dependentTables = array(
		'Page_Model_DbTable_Block'
	);	
}