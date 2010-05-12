<?php
class Agregator_Model_Agregator extends Promotor_Model_Scaffold
{
	protected $_dbTableClass = 'Agregator_Model_DbTable_Agregator';
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function add(array $data)
	{
		$row = $this->getDbTable()
					->createRow($data);
		
		try {
			$row->save();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @param intger|Zend_Db_Table_Row_Abstract $id
	 * @param array $data
	 * @return void
	 */
	public function edit($id, array $data)
	{
		$row = $this->findByPK($id);
		if (!($row instanceof Zend_Db_Table_Row_Abstract))
		{
			$this->_addMessage('rekord o podanym ID nie istnieje');
			$this->_setStatus(self::FAILURE);
			return;
		}

		$row->setFromArray($data);

		try {
			$row->save();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/* (non-PHPdoc)
	 * @see branches/catalog/Promotor/Model/Promotor_Model_Scaffold#delete($row)
	 */
	public function delete($row)
	{
		if (!$row instanceof Zend_Db_Table_Row_Abstract)
		{
			$row = $this->findByPK($row);
			if (!$row instanceof Zend_Db_Table_Row_Abstract)
			{
				$this->_addMessage('rekord nie istnieje lub nie jest instancją "Zend_Db_Table_Row_Abstract"');
				$this->_setStatus(self::FAILURE);
				return;
			}
		}
		
		try {
			$row->delete();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
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

		set_error_handler(array($this, '_errorFeedReader'));
		
		foreach ($feedReader as $entry)
		{
			try {
				$date = $entry->getDateModified();
				// $date może być null
				if (null !== $date)
				{
					$date = $date->toString(Zend_Date::ISO_8601);
				}
			} catch (Exception $e) {
				$this->_addException($e);
				$date = date('Y-m-d H:i:s');
			}
			
			$data = array(
				'feed_id'	   => $feed->id,
				'title'        => $entry->getTitle(),
				'description'  => $entry->getDescription(),
				'dateModified' => $date,
				'link'         => $entry->getLink(),
			);

			try {
				$table->insert($data);
			} catch (Zend_Db_Exception $e) {
				$this->_addException($e);
				$this->_setStatus(self::FAILURE);
			}
		}
		
		restore_error_handler();
	}
	
	/**
	 * Uchwyt błedu podczas importowania feedów
	 */
	protected function _errorFeedReader($errno, $errstr, $errfile, $errline)
	{
		$this->_addMessage("$errstr [$errno]");
	}
	
	/**
	 * Zapisywanie wpisów z wszystkich feedów
	 * @return void
	 */
	public function multiAgregate()
	{
		$table = $this->getDbTable();
		$feed = new Agregator_Model_DbTable_Feed();

		try {
			$rowset = $feed->fetchAll();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		if (!count($rowset))
		{
			return;
		}

		foreach ($rowset as $row)
		{
			$this->agregate($row);
		}
	}
}