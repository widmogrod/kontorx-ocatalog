<?php
class Agregator_Model_DbTable_Agregator extends Zend_Db_Table_Abstract
{
	protected $_name = 'agregator_agregator';

    protected $_referenceMap    = array(
        'Feed' => array(
            'columns'           => 'feed_id',
            'refTableClass'     => 'Agregator_Model_DbTable_Feed',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'title',
            'onDelete'		=> self::CASCADE
        ),
	);
}