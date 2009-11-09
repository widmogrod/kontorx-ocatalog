<?php
class Catalog_Model_Image extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_Image';

	public function selectWithCatalog() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();
		
		$select = new Zend_Db_Select($adapter);
		$select
			->from(array('ci' => 'catalog_image'))
			->joinLeft(array('c' => 'catalog'), 'c.id = ci.catalog_id', array('catalog_name' => 'c.name'));	
			
		return $select;
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function convert($path) {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

		try {
			$rowset = $table->fetchAll();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		$adapter->beginTransaction();
		
		try {
			foreach ($rowset as $row) {
				$oldFilename = $row->image;
				$newFilename = trim($row->image,'.') . '.jpg';

				$oldPath = $path . '/' . $oldFilename;
				$newPath = $path . '/' . $newFilename;

				if (is_file($oldPath)) {
					if (@rename($oldPath, $newPath)) {
						$row->image = $newFilename;
						$row->save();
						$this->_addMessage('YES_NEW::' . $newPath);
					} else {
						$this->_addMessage('NO_NEW::' . $newPath);
					}
				} else {
					$this->_addMessage('NO::' . $oldPath);
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
	 * @param $primaryKey
	 * @return void
	 */
	public function setMainImage($primaryKey) {
		$table = $this->getDbTable();
		
		try {
			$row = $table->find($primaryKey)->current();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_addMessage('Row is not instance of Zend_Db_Table_Row_Abstract');
			$this->_setStatus(self::FAILURE);
			return;
		}

		try {
			$catalog = $row->findParentRow('Catalog_Model_DbTable_Catalog');
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		if (!$catalog instanceof Zend_Db_Table_Row_Abstract) {
			$this->_addMessage('Catalog row is not instance of Zend_Db_Table_Row_Abstract');
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		try {
			$catalog->catalog_image_id = $row->id;
			$catalog->save();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}