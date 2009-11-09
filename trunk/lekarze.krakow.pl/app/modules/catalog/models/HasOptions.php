<?php
class Catalog_Model_HasOptions extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_HasOptions';

	protected $_cachedMethods = array(
		'fetchAllAsPair'
	);
	
	/**
	 * @param integer $primaryKey
	 * @return array
	 */
	public function fetchAllAsPair($primaryKey) {
		if (!is_numeric($primaryKey)) {
			$this->_setStatus(self::FAILURE);
			return;
		}

		$result = array();
		$table = $this->getDbTable();

		$select = $table->select();
		if (null !== $primaryKey) {
			$select->where('catalog_id = ?', $primaryKey, Zend_Db::INT_TYPE);
		}

		try {
			$rowset = $table->fetchAll($select);
		} catch(Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		foreach ($rowset as $row) {
			$result[$row->catalog_options_id] = $row->catalog_options_id;
		}

		$this->_setStatus(self::SUCCESS);
		return $result;
	}

	/**
	 * @param integer $catalogId
	 * @param array $options
	 * @return void
	 */
	public function saveOptions($catalogId, array $options) {
		if (!is_numeric($catalogId)) {
			$this->_addMessage('Catalog ID is not integer!');
			$this->_setStatus(self::FAILURE);
			return;
		}

		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

		$adapter->beginTransaction();

		$where = $adapter->quoteInto('catalog_id = ?', $catalogId, Zend_Db::INT_TYPE);
		$table->delete($where);

		try {
			foreach ($options as $optionId) {
				$data = array(
					'catalog_id' => (int) $catalogId,
					'catalog_options_id' => (int) $optionId,
				);
				
				$table->insert($data);
			}
			$adapter->commit();
			$this->_setStatus(self::SUCCESS);
		} catch (Exception $e) {
			$adapter->rollBack();

			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}