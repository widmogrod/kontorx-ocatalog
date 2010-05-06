<?php
class Agregator_Model_DbTable_Feed extends Zend_Db_Table_Abstract
{
	protected $_name = 'agregator_feed';
	
	protected $_dependentTables = array(
        'Agregator_Model_DbTable_Agregator'
    );
}