<?php
class Catalog_Model_Observer_AttacheDistrict extends Promotor_Observable_Observer_Abstract
{
	public function update(Promotor_Observable_List $list, Zend_Db_Table_Row_Abstract $row = null)
	{
		if(!isset($_POST['catalog_district_id']))
		{
			$this->_setStatus(self::FAILURE);
			$this->_addMessage('Dane POST nie zostąły przekazane w odpowiednim kluczu');
			return;
		}
		
		if(!isset($_POST['catalog_district_id']['chosen_districts']))
		{
			$this->_setStatus(self::FAILURE);
			$this->_addMessage('Dane POST nie zostąły przekazane w odpowiednim kluczu!');
			return;
		}

		$catalogId = $row->id;
		$districts = (array) $_POST['catalog_district_id']['chosen_districts'];
		
		$model = new Catalog_Model_HasDistrict();
		$model->attache($catalogId, $districts);
		
		$this->_setStatus($model->getStatus());
		$this->_addMessages($model->getMessages());
	}
}