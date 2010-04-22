<?php
class Catalog_Model_Certificate extends Promotor_Model_Abstract
{
	protected $_dbTableClass = 'Catalog_Model_DbTable_Certificate';

	
	/**
	 * Sprawdź czy został przydzielony certyfikat dla wizytówki
	 * 
	 * @param int $catalogId
	 * @return bool
	 */
	public function isEnabled($catalogId)
	{
		$table = $this->getDbTable();
		$where = $table->select()->where('catalog_id = ?', $catalogId, Zend_Db::INT_TYPE);
		try {
			$row = $table->fetchRow($where);
		} catch(Zend_Db_Exception $e) {
			$this->_logException($e);
		}

		return ($row instanceof Zend_Db_Table_Row_Abstract);
	}
	
	/**
	 * Włącz certyfikat dla wizytówki
	 * 
	 * @param int $catalogId
	 * @return bool
	 */
	public function enable($catalogId)
	{
		// jest włączony certyfikat
		if ($this->isEnabled($catalogId))
			return true;
			
		$table = $this->getDbTable();
		$row = $table->createRow(array(
			'catalog_id' => (int) $catalogId
		));
		
		try {
			$row->save();
		} catch(Zend_Db_Exception $e) {
			$this->_logException($e);
			return false;
		}

		return true;
	}

	/**
	 * Wyłącz certyfikat dla wizytówki
	 * 
	 * @param int $catalogId
	 * @return bool
	 */
	public function disable($catalogId)
	{
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

		$where = $adapter->quoteInto('catalog_id = ?', $catalogId, Zend_Db::INT_TYPE);

		try {
			$row = $table->delete($where);
		} catch(Zend_Db_Table_Abstract $e) {
			$this->_logException($e);
			return false;
		}
		
		return true;
	}
}