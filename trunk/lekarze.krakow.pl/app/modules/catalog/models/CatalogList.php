<?php
class Catalog_Model_CatalogList extends Promotor_Model_Abstract {
	
	protected $_dbTableClass = 'Catalog_Model_DbTable_Catalog';

	protected $_cachedMethods = array(
		'findAll',
		'findAllByDistrict',
		'findAdress',
		'findAllPremium',
		'findAllGMap',
		'findAllSitemap'
	);
	
	/**
	 * @return Zend_Db_Select
	 */
	protected function _select() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

        $select = new Zend_Db_Select($adapter);

        $select
	        ->from(array('c' => 'catalog'),'*')
	        ->join(array('cd' => 'catalog_district'),
	            'cd.id = c.catalog_district_id',
	            array('district_url' => 'cd.url',
	                  'district' => 'cd.name'))
	        ->joinLeft(array('cpt' => 'catalog_promo_time'),
	            'c.id = cpt.catalog_id '.
	            'AND cpt.catalog_promo_type_id <> 3 '. // bez PREMIUM, <> bo NULL też!
	            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
	            array('cpt.catalog_promo_type_id'))
	            
	        /** Opcje */
	        ->joinLeft(array('co1' => 'catalog_options'),
	            'co1.id = c.catalog_option1_id',
	            array('option1'=>'co1.name'))
	        ->joinLeft(array('co2' => 'catalog_options'),
	            'co2.id = c.catalog_option2_id',
	            array('option2'=>'co2.name'))
	        ->joinLeft(array('co3' => 'catalog_options'),
	            'co3.id = c.catalog_option3_id',
	            array('option3'=>'co3.name'))
	            
	        ->joinLeft(array('ci' => 'catalog_image'),
	            'ci.id = c.catalog_image_id',
	            array('image' => 'ci.image'))

	        
	        ->order('cpt.catalog_promo_type_id DESC')
	        ->order('c.idx DESC')

	        ->where('c.publicated = 1');

        return $select;
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	protected function _selectPremium() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

        $select = new Zend_Db_Select($adapter);

        $select
	        ->from(array('c' => 'catalog'),'*')
	        ->joinInner(array('cd' => 'catalog_district'),
	            'cd.id = c.catalog_district_id',
	            array('district_url' => 'cd.url',
	                  'district' => 'cd.name'))
	        ->joinInner(array('cpt' => 'catalog_promo_time'),
	            'c.id = cpt.catalog_id '.
	            'AND cpt.catalog_promo_type_id = 3 '. // PREMIUM!
	            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
	            array('cpt.catalog_promo_type_id'))
	
			/** Opcje */
	        ->joinLeft(array('co1' => 'catalog_options'),
	            'co1.id = c.catalog_option1_id',
	            array('option1'=>'co1.name'))
	        ->joinLeft(array('co2' => 'catalog_options'),
	            'co2.id = c.catalog_option2_id',
	            array('option2'=>'co2.name'))
	        ->joinLeft(array('co3' => 'catalog_options'),
	            'co3.id = c.catalog_option3_id',
	            array('option3'=>'co3.name'))
	
	        ->joinLeft(array('ci' => 'catalog_image'),
	            'ci.id = c.catalog_image_id',
	            array('image' => 'ci.image'))

	        
	        ->order('cpt.catalog_promo_type_id DESC')
	        ->order('c.idx DESC')
	        
	        ->where('c.publicated = 1');

        return $select;
	}

	/**
	 * @return Zend_Db_Select
	 */
	protected function _selectAll() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

        $select = new Zend_Db_Select($adapter);

        $select
	        ->from(array('c' => 'catalog'),array('name','id','lat','lng'))
	        ->join(array('cd' => 'catalog_district'),
	            'cd.id = c.catalog_district_id',
	            array('district_url' => 'cd.url',
	                  'district' => 'cd.name'))

	        ->joinLeft(array('cpt' => 'catalog_promo_time'),
	            'c.id = cpt.catalog_id '.
	            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
	            array('cpt.catalog_promo_type_id'))

	        
	        ->order('cpt.catalog_promo_type_id DESC')
	        ->order('c.idx DESC')

	        ->where('c.publicated = 1');

        return $select;
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	protected function _selectAllKML() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

        $select = new Zend_Db_Select($adapter);

        $select
	        ->from(array('c' => 'catalog'),array('name','adress','id','lat','lng'))
	        ->join(array('cd' => 'catalog_district'),
	            'cd.id = c.catalog_district_id',
	            array('district_url' => 'cd.url',
	                  'district' => 'cd.name'))

	        ->joinLeft(array('cpt' => 'catalog_promo_time'),
	            'c.id = cpt.catalog_id ',
// wszystkie wizytówki widoczne
//	            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
	            array('cpt.catalog_promo_type_id'))

	        ->joinLeft(array('ci' => 'catalog_image'),
	            'ci.id = c.catalog_image_id',
	            array('image' => 'ci.image'))
	       
	        ->order('cpt.catalog_promo_type_id DESC')
	        ->order('c.idx DESC')

	        ->where('c.publicated = 1');

        return $select;
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	protected function _selectSitemap() {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

        $select = new Zend_Db_Select($adapter);

        $select
	        ->from(array('c' => 'catalog'),'*')
	        ->join(array('cd' => 'catalog_district'),
	            'cd.id = c.catalog_district_id',
	            array('district_url' => 'cd.url',
	                  'district' => 'cd.name'))
	        ->joinLeft(array('cpt' => 'catalog_promo_time'),
	            'c.id = cpt.catalog_id '.
	            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
	            array('cpt.catalog_promo_type_id'))
	
			
	        ->order('cpt.catalog_promo_type_id DESC')
	        ->order('c.idx DESC')

	        ->where('c.publicated = 1');

        return $select;
	}
	
	/**
	 * @param integer $page
	 * @param integer $rowCount
	 * @return array (array, Zend_Db_Select)
	 */
	public function findAll($page, $rowCount) {
		$select = $this->_select();
		$select->limitPage($page, $rowCount);
		
		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			$this->_setStatus(self::SUCCESS);
			return array($rowset, $select, null);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @param string $district
	 * @param integer $page
	 * @param integer $rowCount
	 * @return array (array, Zend_Db_Select, $row)
	 */
	public function findAllByDistrict($district, $page, $rowCount) {
		/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
		$row = null;

		$dbTable = new Catalog_Model_DbTable_District();

		try {
			if (is_integer($district)) {
				$row = $dbTable->find($district)->current();
			} elseif (strlen($district) > 2) {
				$where = $dbTable->select()->where('url = ?', $district);
				$row = $dbTable->fetchRow($where);
			}
		} catch (Zend_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}

		/**
		 * Bulding SQL 
		 */
		
		$select = $this->_select();
		$select->limitPage($page, $rowCount);
		
 		// szukaj w kategorii
		if (is_integer($district)) {
			$select->where('c.catalog_district_id = ?', $district, Zend_Db::INT_TYPE);
		} elseif (strlen($district) > 2) {
			$select->where('cd.url = ?', $district);
		}
		
		// szukaj podkategorii
		if ($row instanceof KontorX_Db_Table_Tree_Row_Abstract) {
			$select->orWhere('cd.path LIKE ?', ltrim($row->path . '/' . $row->id,'/') . '%');
			/*$db = $dbTable->getAdapter();
			$where = array();
            try {
                foreach ($row->findChildrens() as $row) {
                    $where[] = $db->quoteInto('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
                }
                $select->where(implode(" OR ", $where));
            } catch (Exception $e) {
            	var_dump($e);
                $this->_addException($e);
				$this->_setStatus(self::FAILURE);
            }*/
		}

		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return array($rowset, $select, $row->toArray());
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @param string $adress
	 * @return array
	 */
	public function findAdress($adress) {
		$table = $this->getDbTable();

		/* @var $select Zend_Db_Select */
		$select = $table->select()
			->from('catalog', array('adress'))
			->where('adress LIKE ?', '%' . $adress . '%')
			->where('publicated = 1')
			->group('adress');

		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return $rowset;
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @param string $district
	 * @param integer $page
	 * @param integer $rowCount
	 * @param bool $random
	 * @return array
	 */
	public function findAllPremium($district = null, $page = null, $rowCount = null, $random = null) {
		/* @var $row KontorX_Db_Table_Tree_Row_Abstract */
		$row = null;

		$dbTable = new Catalog_Model_DbTable_District();

		try {
			if (is_integer($district)) {
				$row = $dbTable->find($district)->current();
			} elseif (strlen($district) > 2) {
				$where = $dbTable->select()->where('url = ?', $district);
				$row = $dbTable->fetchRow($where);
			}
		} catch (Zend_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}

		/**
		 * Bulding SQL 
		 */
		
		$select = $this->_selectPremium();

		if (is_numeric($page)) {
			$rowCount = (null === $rowCount) ? 5 : (int) $rowCount;
			$select->limitPage((int) $page, $rowCount);
		}

		if ($random) {
			$select
				->reset(Zend_Db_Select::ORDER)
				->order(new Zend_Db_Expr('RAND()'));
		} else {
			$select->order('c.idx DESC');
		}

		// szukaj w kategorii
		if (is_integer($district)) {
			$select->where('c.catalog_district_id = ?', $district, Zend_Db::INT_TYPE);
		} elseif (strlen($district) > 2) {
			$select->where('cd.url = ?', $district);
		}

		// szukaj podkategorii
		if ($row instanceof KontorX_Db_Table_Tree_Row_Abstract) {
			$select->orWhere('cd.path LIKE ?', ltrim($row->path . '/' . $row->id,'/') . '%');
		}
		
		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return $rowset;
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @return array
	 */
	public function findAllGMap() {
		/* @var $select Zend_Db_Select */
		$select = $this->_selectAll();

		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return $rowset;
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @param string $json
	 * @return void
	 */
	public function saveGMapCache($json) {
		try {
            $write = new KontorX_File_Write();
            $write->getChmod(0666);
            $write->setBasedir(PUBLIC_PATHNAME);
            $write->setForce(true);
            $write->write('/data/gmap.json', $json);
            $this->_setStatus(self::SUCCESS);
		} catch(KontorX_File_Exception $e) {
			Zend_Registry::get('logger')->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @return unknown_type
	 */
	public function clearGMapCache() {
		$filename = PUBLIC_PATHNAME . '/data/gmap.json';
		if (is_file($filename)) {
			if (false !== @unlink($filename)) {
				$this->_setStatus(self::SUCCESS);
			}
		}
		$this->_setStatus(self::FAILURE);
	}
	
	/**
	 * @return array
	 */
	public function findAllKml($page, $onpage) {
		/* @var $select Zend_Db_Select */
		$select = $this->_selectAllKML();

		$select->limitPage($page, $onpage);
		
		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return $rowset;
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}
	
	/**
	 * @return array
	 */
	public function findAllSitemap() {
		/* @var $select Zend_Db_Select */
		$select = $this->_selectSitemap();

		$rowset = array();
		
		try {
			$stmt = $select->query();
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
			while ($row = $stmt->fetch()) {
				$rowset[] = array(
					'label' => $row['name'],
					'visible' => true,
					'route' => 'catalog-show',
					'params' => array(
						'id' => $row['id']
					)
				);
			}
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
		
		return $rowset;
	}
	
	/**
	 * @param string $sitemap
	 * @return void
	 */
	public function saveCacheSitemap($sitemap) {
		try {
			$write = new KontorX_File_Write();
			$write->setChmod(0666);
			$write->setBasedir(PUBLIC_PATHNAME);
			$write->write('sitemap.xml', $sitemap);
			$this->_setStatus(self::SUCCESS);
		} catch(KontorX_File_Exception $e) {
			Zend_Registry::get('logger')->log($e->getMessage(), Zend_Log::ERR);
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}

	/**
	 * @return void
	 */
	public function clearCacheSitemap() {
		$path = PUBLIC_PATHNAME . DIRECTORY_SEPARATOR . 'sitemap.xml';
		if (is_file($path)) {
			if (false === @unlink($path)) {
				$message = function_exists('error_get_last')
					? error_get_last()
					: sprintf('nie usunięto sitemap "%s"', $path);

				$this->_addMessage($message);
				$this->_setStatus(self::FAILURE);
			}
		}
		$this->_setStatus(self::SUCCESS);
	}
}