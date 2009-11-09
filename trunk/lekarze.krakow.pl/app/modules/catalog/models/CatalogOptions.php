<?php
require_once 'KontorX/Db/Table/Tree/Abstract.php';
class CatalogOptions extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_options';

    protected $_dependentTables = array(
                'CatalogHasCatalogOptions'
    );

    protected $_cachedMethods = array('fetchAllOptionsArray');

    /**
     * Tablica dla opcji dla @see Zend_View_Helper_FormMuliCheckbox
     * @return array
     */
    public function fetchAllOptionsArray() {
        $result = array();
        try {
            $rowset = $this->fetchAll();
            foreach ($rowset as $row) {
                $result[$row->id] = $row->name;
            }
        } catch (Zend_Db_Table_Exception $e) {
            Zend_Registry::get('logger')
            ->log($e->getMessage() ."\n".$e->getTraceAsString(), Zend_Log::ERR);
        }

        return $result;
    }
    
	/**
     * Tablica dla semantycznego wyszukiwania @see KontorX_Search_Semantic_Interpreter_ArrayKeyValueExsists
     * @return array
     */
    public function fetchAllArrayKeyValueExsists() {
        $result = array();
        try {
            $rowset = $this->fetchAll();
            foreach ($rowset as $row) {
                $result[] = array('key' => $row->name, 'value' => $row->id);
            }
        } catch (Zend_Db_Table_Exception $e) {
            Zend_Registry::get('logger')
            ->log($e->getMessage() ."\n".$e->getTraceAsString(), Zend_Log::ERR);
        }

        return $result;
    }
}