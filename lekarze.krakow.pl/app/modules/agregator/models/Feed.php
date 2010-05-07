<?php
class Agregator_Model_Feed extends Promotor_Model_Scaffold
{
	protected $_dbTableClass = 'Agregator_Model_DbTable_Feed';

	/**
	 * @param string $url
	 * @return void
	 */
	public function add($url)
	{
		try {
			$feed = Zend_Feed_Reader::import($url);
		} catch(Zend_Feed_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}

		$data = array(
			'url'       => $url,
			'copyright' => trim($feed->getCopyright()),
			'link'      => trim($feed->getLink()),
			'title'     => trim($feed->getTitle())
		);

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

		$url = @$data['url'];
		if ($row->url !== $url)
		{
			try {
				$feed = Zend_Feed_Reader::import($url);
			} catch(Zend_Feed_Exception $e) {
				$this->_addException($e);
				$this->_setStatus(self::FAILURE);
				return;
			}
	
			$merge = array(
				'url'       => $url,
				'copyright' => trim($feed->getCopyright()),
				'link'      => trim($feed->getLink()),
				'title'     => trim($feed->getTitle())
			);
			
			$data = array_merge($data, $merge);
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
}