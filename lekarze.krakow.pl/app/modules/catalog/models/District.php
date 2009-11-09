<?php
class Catalog_Model_District extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_District';

	protected $_cachedMethods = array(
		'findAll'
	);
	
	public function findAll() {
		$table = $this->getDbTable();
		
		try {
			$rowset = $table->fetchAll();
			$this->_setStatus(self::SUCCESS);
			return $rowset;
		} catch (Zend_Db_Exception $e) {
			$this->_addMessage($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}