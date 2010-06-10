<?php
set_time_limit(0);
ini_set('memory_limit', '256M');

/**
 * Aktualizacja przenosi powiązania wizytówki (catalog) z dzilnica
 * do tabeli `catalog_has_district`
 * 
 * @author Gabriel
 */
class Catalog_Update_R34 extends KontorX_Update_Abstract
{
	/**
	 * Update
	 * @return void
	 */
	public function up()
	{
		$districtTable = new Catalog_Model_DbTable_District();
		try {
			$districtRowset = $districtTable->select()->query()->fetchAll();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}
		
		$districtRowsetNew = array();
		foreach($districtRowset as $row)
		{
			$districtRowsetNew[$row['id']] = $row;
		}
		$districtRowset = $districtRowsetNew;
		unset($districtRowsetNew);
		
		$catalogTable = new Catalog_Model_DbTable_Catalog();

		try {
			$catalogRowset = $catalogTable->select()->query()->fetchAll();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}

		$hasDistrict = new Catalog_Model_DbTable_HasDistrict();
		
		foreach ($catalogRowset as $row)
		{
			$catalogId  = $row['id'];
			$districtId = $row['catalog_district_id'];
			
			// zbierz wszystkie `id` Miasta i dzielnicy
			// (kolekcjonowanie zagnieżdzonych wpisów)
			$districtIds = array();
			if (isset($districtRowset[$districtId]))
			{
				$districtIds = (array) explode('/', $districtRowset[$districtId]['path']);
				$districtIds = array_filter($districtIds);
			}
			$districtIds[] = $districtId;

			foreach ($districtIds as $districtId)
			{
				$data = array(
					'catalog_id' => $catalogId,
					'district_id' => $districtId
				);
	
				try {
					$hasDistrict->insert($data);
				} catch (Zend_Db_Exception $e) {
					$this->_addException($e);
				}
			}
		}

		$this->_setStatus(self::SUCCESS);
	}

	/**
	 * Downgrade
	 * @return void
	 */
	public function down()
	{
		// nie można cofnąc tej operacji
		$this->_setStatus(self::FAILURE);
	}
}

return new Catalog_Update_R34();