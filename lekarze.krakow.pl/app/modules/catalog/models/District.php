<?php
class Catalog_Model_District extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_District';

	protected $_cachedMethods = array(
		'findById',
		'findChildrens',
		'findParents',
		'findAll',
		'fetchAllArrayKeyValueExsists',
		'findByIdentification'
	);
	
	/**
	 * @param integer|string $identification
	 * @return Zend_Db_Table_Row_Abstract|null
	 */
	public function findByIdentification($identification)
	{
		$table = $this->getDbTable();
		$row = null;
		
		if (is_numeric($identification))
		{
			// odszukaj jeszcze rekord o dzielnicy
			try {
				$row = $table->find($identification)->current();
			} catch(Zend_Db_Exception $e) {}
		} else
		if (is_string($identification)) 
		{
			// odszukaj jeszcze rekord o dzielnicy
			try {
				$row = $table->fetchRow(
							$table->getAdapter()->quoteInto('url = ?', $identification));
			} catch(Zend_Db_Exception $e) {}
		}
		 
		return $row;
	}
	
	/**
	 * @param integer $id
	 * @param bool $asArray
	 * @return array|KontorX_Db_Table_Tree_Row_Abstract
	 */
	public function findById($id, $asArray = true) {
		$table = $this->getDbTable();
		try {
			$rowset = $table->find((int) $id);
			if(!count($rowset)) {
				$this->_setStatus(self::FAILURE);
				return;
			}

			$this->_setStatus(self::SUCCESS);

			$current = $rowset->current();
			return $asArray
				? $current->toArray()
				: $current;
		} catch (Zend_Db_Table_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addMessage($e->getMessage());
		}
	}
	
	/**
	 * @param string|integer|KontorX_Db_Table_Tree_Row_Abstract $row
	 * @return KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	public function findChildrens($row, $depthLevel = null) {
		$attachment = array();

		if (is_int($row)) {
			$row = $this->findById($row, false);
		}

		if (!$row instanceof KontorX_Db_Table_Tree_Row_Abstract) {
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
		$rowset = $row->findChildrens($depthLevel);
		return $rowset;
	}
	
	/**
	 * @param string|integer|KontorX_Db_Table_Tree_Row_Abstract $row
	 * @return KontorX_Db_Table_Tree_Rowset_Abstract
	 */
	public function findParents($row, $depthLevel = null) {
		$attachment = array();

		if (is_int($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}

		if (!$row instanceof KontorX_Db_Table_Tree_Row_Abstract) {
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
		$rowset = $row->findParents($depthLevel);
		return $rowset;
	}
	
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
	
	/**
     * Tablica dla semantycznego wyszukiwania @see KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsists
     * @return array
     */
    public function fetchAllArrayKeyValueExsists() {
    	$table = $this->getDbTable();

        try {
            $rowset = $table->fetchAll();
        } catch (Zend_Db_Table_Exception $e) {
        	$this->_addException($e);
        	$rowset = array();
        }

        $result = array();
    	foreach ($rowset as $row) {
			$result[] = array('key' => $row->name, 'value' => $row->id);
		}
        return $result;
    }
}