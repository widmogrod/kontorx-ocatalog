<?php
class Catalog_Model_Searching extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_Searching';

	protected $_cachedMethods = array(
	);
	
	public function selectList() {
		return $this->getDbTable()->select();
	}
}