<?php
class Agregator_Model_Agregator extends Promotor_Model_Scaffold
{
	protected $_dbTableClass = 'Agregator_Model_DbTable_Agregator';
	
	public function add()
	{
		
	}
	
	public function edit()
	{
		
	}
	
	public function delete()
	{
		
	}

	/**
	 * Zbieranie wpisów z określonego feedu
	 * 
	 * @param Zend_Db_Table_Row_Abstract|int $feed
	 * @return void
	 */
	public function agregate($feed)
	{
		if (!$feed instanceof Zend_Db_Table_Row_Abstract)
		{
			$model = new Agregator_Model_Feed();
			$feed = $model->findByPK($feed);
			
			if (!$feed instanceof Zend_Db_Table_Row_Abstract)
			{
				$this->_addMessage('feed by PK "'.$feed.'" do not exsists');
				$this->_setStatus(self::FAILURE);
				return;
			}
		}

		try {
			$feedReader = Zend_Feed_Reader::import($feed->url);
		} catch (Zend_Feed_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}

		$table = $this->getDbTable();
		
		foreach ($feedReader as $entry)
		{
			$data = array(
				'feed_id'	   => $feed->id,
				'title'        => $entry->getTitle(),
				'description'  => $entry->getDescription(),
				'dateModified' => $entry->getDateModified(),
				'link'         => $entry->getLink(),
			);

			try {
				$table->insert($data);
			} catch (Zend_Date_Exception $e) {
				$this->_addException($e);
				$this->_setStatus(self::FAILURE);
			}
		}
	}
	
	/**
	 * Zapisywanie wpisów z wszystkich feedów
	 * @return void
	 */
	public function multiAgregate()
	{
		$table = $this->getDbTable();
		$feed = new Agregator_Model_Feed();

		try {
			$rowset = $feed->fetchAll();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		foreach ($rowset as $row)
		{
			$this->agregate($row);
		}
	}
}