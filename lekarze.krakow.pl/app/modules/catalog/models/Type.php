<?php
class Catalog_Model_Type extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_Type';
	
	protected $_cachedMethods = array(
		'findById',
		'findByAlias',
		'findAll',
		'findAllAsCatalogList'
	);
	
	/**
	 * @param string $alias
	 * @param bool $asArray
	 * @return array
	 */
	public function findById($id, $asArray = true) {
		$table = $this->getDbTable();
		$where = $table->getAdapter()->quoteInto('id = ?', $id);

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
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}	

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
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @param integer $page
	 * @param integer $rowCount
	 * @return array
	 */
	public function findAll($page = null, $rowCount = null) {
		$table = $this->getDbTable();
		
		$select = $table->select();
		$select->order('name');
		
		if (is_integer($page)) {
			$select->limitPage($page, $rowCount);
		}
		
		try {
			$rowset = $table->fetchAll($select);

			$this->_setStatus(self::SUCCESS);
			return $rowset->toArray();
		} catch (Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	protected function _selectType($typeId) {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

        $select = new Zend_Db_Select($adapter);

        $select
        	->distinct(true)
	        ->from(array('c' => 'catalog'))
	        ->joinInner(array('cd' => 'catalog_district'),
	            'cd.id = c.catalog_district_id',
	            array('district_url' => 'cd.url',
	                  'district' => 'cd.name'))

	        ->joinLeft(array('cpt' => 'catalog_promo_time'),
	            'c.id = cpt.catalog_id '.
	            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
	            array('cpt.catalog_promo_type_id'))

//	        ->joinInner(array('cs' => 'catalog_service'),
//	        	$adapter->quoteInto('cs.id = ?', (int) $typeId), array())
	            
			/** Opcje */
	        ->joinLeft(array('co1' => 'catalog_options'),
	            'co1.id = c.catalog_option1_id',
	            array('option1'=>'co1.name'))
	        ->joinLeft(array('co2' => 'catalog_options'),
	            'co2.id = c.catalog_option2_id',
	            array('option2'=>'co2.name'))
	        ->joinLeft(array('co3' => 'catalog_options'),
	            'co3.id = c.catalog_option3_id',
	            array('option3'=>'co3.name'))
	
	        ->joinLeft(array('ci' => 'catalog_image'),
	            'ci.id = c.catalog_image_id',
	            array('image' => 'ci.image'))

	        ->order('cpt.catalog_promo_type_id DESC')
	        ->where('c.catalog_type_id = ?', $typeId)
	        ->where('c.publicated = 1');

        return $select;
	}

	/**
	 * @param integer|string|Zend_Db_Table_Row_Abstract $row
	 * @param $page
	 * @param $rowCount
	 * @return unknown_type
	 */
	public function findAllAsCatalogList($row, $page, $rowCount) {
		if (is_numeric($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}

		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_addMessage('Row not instanceof Zend_Db_Table_Row_Abstract');
			$this->_setStatus(self::FAILURE);
			return;
		}

		/* @var $select Zend_Db_Select */
		$select = $this->_selectType($row->id);
		$select->limitPage($page, $rowCount);

		try {
			$stmt = $select->query(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			// reset dla paginacji!
			$select
				->reset(Zend_Db_Select::LIMIT_COUNT)
				->reset(Zend_Db_Select::LIMIT_OFFSET);
			
			$this->_setStatus(self::SUCCESS);
			return array($rowset, $select, $row->toArray());
		} catch (Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}