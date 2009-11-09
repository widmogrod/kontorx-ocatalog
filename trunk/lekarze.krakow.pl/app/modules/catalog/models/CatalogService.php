<?php
//require_once dirname(__FILE__) . '/CatalogServiceCost.php';

require_once 'KontorX/Db/Table/Abstract.php';
class CatalogService extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog_service';
//    protected $_rowClass = 'CatalogService_Row';

    protected $_dependentTables = array(
                'CatalogServiceCost'
    );

    protected $_cachedMethods = array('fetchAllOptionsArray');

    /**
     * Tablica opcji dla @see Zend_View_Helper_FormMuliCheckbox
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

require_once 'KontorX/Db/Table/Row/FileUpload/Abstract.php';
class CatalogService_Row extends KontorX_Db_Table_Row_FileUpload_Abstract {
    protected $_filesKeyName = 'ico';
    protected $_fieldFilename = 'ico';

    protected $_noUploadException = false;

    //	public function init() {
    //		self::setImagePath('./upload/catalog/ico_service/');
    //		parent::init();
    //	}

    public function setNoUploadException($flag = true) {
        $this->_noUploadException = (bool) $flag;
    }

    public function _insert() {
        parent::_insert();

        if ($this->hasMessages() && !$this->_noUploadException) {
            require_once 'KontorX/Db/Table/Row/FileUpload/Exception.php';
            $message = implode("\n",$this->getMessages());
            throw new KontorX_Db_Table_Row_FileUpload_Exception($message);
        }
    }
}