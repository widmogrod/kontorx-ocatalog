<?php
class Catalog_Model_District extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_District';

	protected $_cachedMethods = array(
		'findAll',
		'fetchAllArrayKeyValueExsists'
	);
	
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