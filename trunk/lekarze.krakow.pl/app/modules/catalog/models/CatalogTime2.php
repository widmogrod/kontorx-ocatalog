<?php
class Catalog_Model_CatalogTime2 extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_Time';

	protected $_cachedMethods = array(
	);
	
	/**
	 * @param integer $catalogId
	 * @param array $weeks
	 * @return void
	 */
	public function setWeekTime($catalogId, array $weeks) {
		if (!is_numeric($catalogId)) {
			$this->_addMessage('Catalog ID is not integer!');
			$this->_setStatus(self::FAILURE);
			return;
		}

		$v = new KontorX_Validate_Hour();
		$v->setDisableTranslator(true);

		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

		$adapter->beginTransaction();

		$where = $adapter->quoteInto('catalog_id = ?', $catalogId, Zend_Db::INT_TYPE);
		$table->delete($where);

		try {
//			var_dump($weeks);
			foreach ($weeks as $weekNumber => $weekData) {
				// 1 - Monday ... 7 - Sundey
				var_dump($weekNumber);
				if ($weekNumber > 0 && $weekNumber < 8) {
					var_dump($weekData);
					if ($v->isValid($weekData['START'])
							&& $v->isValid($weekData['END'])) {
						
						$dataStart = array(
							'catalog_id' => (int) $catalogId,
							'start_end' => 'START',
							'time' => $weekData['START'],
							'day' => (int) $weekNumber,
						);
						
						$dataEnd = array(
							'catalog_id' => (int) $catalogId,
							'start_end' => 'END',
							'time' => $weekData['END'],
							'day' => (int) $weekNumber,
						);

						$table->insert($dataStart);
						$table->insert($dataEnd);
					}
				}
			}
			$adapter->commit();
		} catch (Exception $e) {
			$adapter->rollBack();

			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @return unknown_type
	 */
	public function convert() {
		require_once 'catalog/models/CatalogTime.php';
		$model1 = new CatalogTime();

		$table = $this->getDbTable();

		$adapter = $table->getAdapter();
		$adapter->beginTransaction();

		try {
			foreach ($model1->fetchAll() as $row) {
				$table->delete($adapter->quoteInto('catalog_id = ?', $row->catalog_id));
	
				if ($row->monday_start !== '00:00:00' && $row->monday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 1);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->monday_start;
					$dataEnd['time'] = $row->monday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
	
				if ($row->tuesday_start !== '00:00:00' && $row->tuesday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 2);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->tuesday_start;
					$dataEnd['time'] = $row->tuesday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
				
				if ($row->wednesday_start !== '00:00:00' && $row->wednesday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 3);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->wednesday_start;
					$dataEnd['time'] = $row->wednesday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
				
				if ($row->thursday_start !== '00:00:00' && $row->thursday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 4);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->thursday_start;
					$dataEnd['time'] = $row->thursday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
				
				if ($row->friday_start !== '00:00:00' && $row->friday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 5);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->friday_start;
					$dataEnd['time'] = $row->friday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
				
				if ($row->saturday_start !== '00:00:00' && $row->saturday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 6);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->saturday_start;
					$dataEnd['time'] = $row->saturday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
				
				if ($row->sunday_start !== '00:00:00' && $row->sunday_end !== '00:00:00') {
					$dataStart = $dataEnd = array('day' => 7);
	
					$dataStart['catalog_id'] = $row->catalog_id;
					$dataEnd['catalog_id'] = $row->catalog_id;
					
					$dataStart['start_end'] = 'START';
					$dataEnd['start_end'] = 'END';
	
					$dataStart['time'] = $row->sunday_start;
					$dataEnd['time'] = $row->sunday_end;
					
					$table->insert($dataStart);
					$table->insert($dataEnd);
				}
			}
			
			$adapter->commit();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$adapter->rollBack();
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	public function selectList() {
		$table = $this->getDbTable();
		$db = $table->getAdapter();
		
		$select = new Zend_Db_Select($db);
		$select->from(array('ct' => $table->info(Zend_Db_Table::NAME)))
			   ->joinInner(array('c' => 'catalog'), 'ct.catalog_id = c.id', array('catalog_name' => 'name'));

		return $select;
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