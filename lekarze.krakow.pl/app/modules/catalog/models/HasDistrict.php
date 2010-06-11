<?php
class Catalog_Model_HasDistrict extends Promotor_Model_Abstract 
{
	protected $_dbTableClass = 'Catalog_Model_DbTable_HasDistrict';

	/**
	 * @param integer $catalogId
	 * @param array $districts
	 * @return void
	 */
	public function attache($catalogId, array $districts) 
	{
		if (!is_numeric($catalogId)) 
		{
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
			foreach ($districts as $k => $districtId) 
			{
				$data = array(
					'catalog_id' => (int) $catalogId,
					'district_id' => (int) $districtId
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