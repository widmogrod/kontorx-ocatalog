<?php
class Catalog_Model_SiteFrame extends Promotor_Model_Abstract {

	protected $_dbTableClass = 'Catalog_Model_DbTable_SiteFrame';
	
	protected $_cachedMethods = array(
		'findByAlias'
	);

	/**
	 * @param string $alias
	 * @param bool $asArray
	 * @return array
	 */
	public function findByAlias($alias, $asArray = true) {
		$table = $this->getDbTable();
		$where = $table->getAdapter()->quoteInto('alias = ?', $alias);

		try {
			$row = $table->fetchRow($where);
			if(!$row instanceof Zend_Db_Table_Row_Abstract) {
				$this->_setStatus(self::FAILURE);
				return;
			}
			$this->_setStatus(self::SUCCESS);
			return $asArray
				? $row->toArray()
				: $row;
		} catch (Zend_Db_Table_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function editableUpdate(array $data) {
		$table = $this->getDbTable();
		$db = $table->getAdapter();

		$parentKey = $table->info(Zend_Db_Table::PRIMARY);
		$whereBase = implode(' = %s AND ', $parentKey) . ' = %s';
		
		$db->beginTransaction();
		try {
			foreach ($data as $key => $values) {
				$key = explode(KontorX_DataGrid_Cell_Editable_Abstract::SEPARATOR, $key);
				$where = vsprintf($whereBase, $key);
				$table->update($values, $where);
			}
			$db->commit();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Table_Exception $e) {
			$db->rollBack();
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function editableDelete(array $data) {
		$table = $this->getDbTable();
		$db = $table->getAdapter();

		$parentKey = $table->info(Zend_Db_Table::PRIMARY);
		$whereBase = implode(' = %s AND ', $parentKey) . ' = %s';
		
		$db->beginTransaction();
		try {
			foreach ($data as $key => $values) {
				// większe od zera .. zakładam że autoincrement jest od 1..
				if (isset($values['id']) && $values['id'] > 0) {
					// TODO Nalezy pamiętać o tym że mogą to być rekordy z tree..
					$key = explode(KontorX_DataGrid_Cell_Editable_Abstract::SEPARATOR, $key);
					$where = vsprintf($whereBase, $key);
					$table->delete($where);
				}
			}
			$db->commit();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Table_Exception $e) {
			$db->rollBack();
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
}