<?php
class Catalog_Model_SearchFind extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_SearchFind';

	protected $_cachedMethods = array(
	);
	
	public function selectList() {
		$db = $this->getDbTable()->getAdapter();
		
		$select = new Zend_Db_Select($db);
		$select->from(array('csf' => 'catalog_search_find'))
			   ->joinInner(array('cs' => 'catalog_search'), 'cs.id = csf.query_search_id', array('query'))
			   ->joinInner(array('c' => 'catalog'), 'c.id = csf.catalog_id', array('catalog_name' => 'name'))

			   // podwóje sortowanie zapewnia
			   // że grupowanie w dataGrid dobrze jest wizualizoawne
			   ->order('csf.query_search_id ASC')
			   ->order('csf.idx DESC');

		return $select;
	}
}