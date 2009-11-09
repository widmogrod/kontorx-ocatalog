<?php
class Catalog_Model_ServiceCost extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_ServiceCost';

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
	 * @param integer $catalogId
	 * @param array $services
	 * @return void
	 */
	public function saveOptions($catalogId, array $services) {
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
			foreach ($service as $serviceId) {
				$data = array(
					'catalog_id' => (int) $catalogId,
					'catalog_service_id' => (int) $serviceId,
					'cost_min' => null, 
					'cost_max' => null 
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