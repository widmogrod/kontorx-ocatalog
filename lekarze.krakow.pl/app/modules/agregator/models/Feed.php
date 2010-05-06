<?php
class Agregator_Model_Feed extends Promotor_Model_Scaffold
{
	protected $_dbTableClass = 'Agregator_Model_DbTable_Feed';

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
			'copyright' => $feed->getCopyright(),
			'link'      => $feed->getLink(),
			'title'     => $feed->getTitle()
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
}