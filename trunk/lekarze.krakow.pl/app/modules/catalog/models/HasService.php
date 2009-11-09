<?php
class Catalog_Model_HasService extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_HasService';

	protected $_cachedMethods = array(
		'fetchAllAsPair'
	);
	
	/**
	 * @return Zend_Db_Select
	 */
	public function selectWithCatalog() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();
		
		$select = new Zend_Db_Select($adapter);
		$select
			->from(array('csc' => 'catalog_service_cost'))
			->joinLeft(array('c' => 'catalog'), 'c.id = csc.catalog_id', array('catalog_name' => 'c.name'))
			->joinLeft(array('cs' => 'catalog_service'), 'cs.id = csc.catalog_service_id', array('service_name' => 'cs.name'));
			
		return $select;
	}
	
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
	 * @param array $service
	 * @return void
	 */
	public function saveService($catalogId, array $service, array $desc = null) {
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

		$filter = new KontorX_Filter_MagicQuotes();
		
		try {
			foreach ($service as $k => $serviceId) {
				$data = array(
					'catalog_id' => (int) $catalogId,
					'catalog_service_id' => (int) $serviceId
				);

				if (isset($desc[$k])) {
					$data['desc'] = $filter->filter($desc[$k]);
				}

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