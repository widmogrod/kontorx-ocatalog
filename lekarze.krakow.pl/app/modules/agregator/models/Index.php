<?php
class Agregator_Model_Index extends Promotor_Model_Abstract
{
	protected $_cachedMethods = array(
		'findAll',
		'findById'
	);

	/**
	 * @var Zend_Db_Select
	 */
	protected $_select;
	
	/**
	 * @return Zend_Db_Select
	 */
	public function getSelect()
	{
		return $this->_select;
	}

	/**
	 * @param integer $page
	 * @param integer $limit
	 * @return Zend_Paginator|null
	 */
	public function getPaginator($page, $limit)
	{
		if ($this->_select)
		{
			$paginator = Zend_Paginator::factory($this->_select);
			$paginator->setItemCountPerPage($limit);
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		}
	}
	
	/**
	 * @param integer $page
	 * @param integer $limit
	 * @return array
	 */
	public function findAll($page = null, $limit = null)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$select = new Zend_Db_Select($db);
		$select->from(array('aa' => 'agregator_agregator'))
				->joinLeft(
					array('af' => 'agregator_feed'),
					'af.id = aa.feed_id', 
					array(
						'feed_title' => 'title',
						'feed_copyright' => 'copyright',
						'feed_link' => 'link',
					))
				->order('dateModified DESC');

		if (!is_numeric($page))
			$page = 1;

		if (!is_numeric($limit))
			$limit = 10;

		$select->limitPage($page, $limit);
			
		// zapisz wykonywane zapytanie select
		$this->_select = $select;
		
		$result = array();

		try {
			$stmt = $select->query();
			$result = $stmt->fetchAll();
		} catch(Zend_Db_Statement_Exception $e) {}
		
		return $result;
	}

	/**
	 * @param integer $id
	 * @return array
	 */
	public function findById($id)
	{
		$result = array();

		if (!is_numeric($id))
		{
			return $result;
		}

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$select = new Zend_Db_Select($db);
		$select->from(array('aa' => 'agregator_agregator'))
				->joinLeft(
					array('af' => 'agregator_feed'),
					'af.id = aa.feed_id', 
					array(
						'feed_title' => 'title',
						'feed_copyright' => 'copyright',
						'feed_link' => 'link',
					))
				->where('aa.id = ?', $id);

		$result = array();
				
		try {
			$stmt = $select->query();
			$result = $stmt->fetch();
		} catch(Zend_Db_Statement_Exception $e) {}

		return $result;
	}
}