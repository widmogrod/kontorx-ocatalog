<?php
class Catalog_Model_SearchQuery extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_SearchQuery';

	protected $_cachedMethods = array(
	);
	
	public function selectList() {
		return $this->getDbTable()
					->select()
					->from('catalog_search_query')
					->columns(array('id','query', 'count' => new Zend_Db_Expr('COUNT(id)')))
					->group('query');
	}
}