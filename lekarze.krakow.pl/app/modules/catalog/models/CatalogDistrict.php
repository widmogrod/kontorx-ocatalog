<?php
// zalerznosci
require_once 'user/models/User.php';
require_once 'language/models/Language.php';

require_once 'KontorX/Db/Table/Tree/Abstract.php';
class CatalogDistrict extends KontorX_Db_Table_Tree_Abstract {
    protected $_name = 'catalog_district';
    protected $_level = 'path';

    protected $_rowClass = 'CatalogDistrict_Row';

    protected $_dependentTables = array(
        'Catalog'
    );

    /**
     * Wyłowienie widocznego publicznie rekordu
     *
     * @param string $url
     * @param string $language
     * @param Zend_Db_Select $select
     * @return KontorX_Db_Table_Tree_Row_Abstract
     */
    public function fetchRowPublic($url, $language, Zend_Db_Select $select = null) {
        $select = (null === $select)
            ? $this->select()
            : $select;

        $select = $this->selectPublic($language, $select)
        ->where('url = ?', $url);

        return $this->fetchRow($select);
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

require_once 'KontorX/Db/Table/Tree/Row.php';
class CatalogDistrict_Row extends KontorX_Db_Table_Tree_Row {
    protected $_level = 'path';
}