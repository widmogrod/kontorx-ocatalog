<?php
class Catalog_Model_Catalog extends Promotor_Model_Abstract {
	protected $_dbTableClass = 'Catalog_Model_DbTable_Catalog';

	protected $_cachedMethods = array(
		'isPromo',
		'findById',
		'findByAlias',
		'findAllTime',
		'findAllOptions',
		'findAllService',
		'findAllStaff',
		'findAllImage',
		'fetchAllAsPair'
	);

	/**
	 * 
	 * @return bool
	 */
	public function isPromo($id) {
		$table = new Catalog_Model_DbTable_PromoTime();

		$where = $table->select(true)
			->where('catalog_id = ?', (int) $id)
			->where('? BETWEEN t_start AND t_end', new Zend_Db_Expr('NOW()'))
			->where('publicated = 1');

		try {
			$result = $table->fetchRow($where);
			$this->_setStatus(self::SUCCESS);
			return ($result instanceof Zend_Db_Table_Row_Abstract);
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);

			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
		}

		return false;
	}
	
	/**
	 * @param integer $id
	 * @param bool $asArray
	 * @return array
	 */
	public function findById($id, $asArray = true) {
		$table = $this->getDbTable();
		try {
			$rowset = $table->find((int) $id);
			if(!count($rowset)) {
				$this->_setStatus(self::FAILURE);
				return;
			}

			$this->_setStatus(self::SUCCESS);

			$current = $rowset->current();
			return $asArray
				? $current->toArray()
				: $current;
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addMessage($e->getMessage());

			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
		}
	}
	
	/**
	 * @param string $alias
	 * @param bool $asArray
	 * @return array
	 */
	public function findByAlias($alias, $asArray = true) {
		return (true === $asArray)
			? $this->_findByAliasAsArray($alias)
			: $this->_findByAliasAsRow($alias);
	}
	
	/**
	 * @param string $alias
	 * @return array
	 */
	protected function _findByAliasAsRow($alias) {
		$table = new Catalog_Model_DbTable_Site();

		$where = $table->getAdapter()->quoteInto('url = ?', $alias); 

		try {
			/* @var $row Zend_Db_Table_Row_Abstract */
			$row = $table->fetchRow($where);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
			$this->_logException($e);
			return;
		}

		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_setStatus(self::FAILURE);
			return;
		}

		try {
			return $row->findParentRow('Catalog_Model_DbTable_Catalog');
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
			$this->_logException($e);
			return;
		}
	}
	
	protected function _findByAliasAsArray($alias) {
		$table = $this->getDbTable();
		$adapter = $table->getAdapter();

		$select = new Zend_Db_Select($adapter);
		$select->from(array('cs'=>'catalog_site'), array())
			   ->joinInner(array('c' => 'catalog'), 'c.id = cs.catalog_id', '*')
			   ->where('cs.url = ?', $alias)
			   ->limit(1);

		try {
			$stmt = $select->query();
			$result = $stmt->fetch();

			$this->_setStatus(self::SUCCESS);
			return $result;
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
		}
	}

	/**
	 * Przygotuj dane dla widuku w formie
	 * 
	 * <code>
	 * array(
	 *  // monday
	 * 	1 => array(
	 * 	 array('start' => '9:00', 'end' => '17:00')
	 * 	 ..
	 *  )
	 *  ..
	 *  // sunday
	 *  7 => array(
	 *   array('start' => '9:00', 'end' => '17:00')
	 *  )
	 * )
	 * </code> 
	 * 
	 * @param integer|string|Zend_Db_Table_Abstract $row
	 * @return arary
	 */
	public function findAllTime($row) {
		if (is_int($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}

		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_setStatus(self::FAILURE);

			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
			return;
		}
		
	  	// HACK for serialized row
		if(!$row->isConnected()) {
			$row->setTable($this->getDbTable());
		}

		// Zapewnienie odpowiedniego sortowania
		$select = $this->getDbTable()->select();
		$select
			->order('day ASC')
			->order('time ASC')
			->order('start_end DESC');

		try {
			$rowset = $row->findDependentRowset('Catalog_Model_DbTable_Time', null, $select);
		} catch (Zend_Db_Exception $e) {
			$this->_setStatus(self::FAILURE);
			$this->_addException($e);
			
			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
			return;
		}
		
		// formatowanie danych

		$data = array();
		$rowset->rewind();

		while ($rowset->valid()) {
			$row = $rowset->current();
			$store = array();

			if ($row->start_end == 'START') {
				$store['start'] = substr($row->time,0,5); // z daty HH:mm:ss -> HH:mm

				$rowset->next();
				if ($rowset->valid()) {
					$row = $rowset->current();

					if ($row->start_end == 'END') {
						$store['end'] = substr($row->time,0,5); // z daty HH:mm:ss -> HH:mm
					} else {
						$store['end'] = null;
					}
				} else {
					$store['end'] = null;
				}
			} else
			if ($row->start_end == 'END') {
				$store['start'] = null;
				$store['end'] = substr($row->time,0,5); // z daty HH:mm:ss -> HH:mm
			}
			
			if (!isset($data[$row->day])) {
				$data[$row->day] = array();
			}
			
			$data[$row->day][] = $store;
			
			$rowset->next();
		}

		return $data;
	}
	
	/**
	 * @param integer|string|Zend_Db_Table_Abstract $row
	 * @return array
	 */
	public function findAllOptions($row) {
		if (is_int($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}
		
		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_setStatus(self::FAILURE);

			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
			return;
		}

		// HACK for serialized row
		if(!$row->isConnected()) {
			$row->setTable($this->getDbTable());
		}
		
		$select = $row->select()->order('name ASC');
		
		try {
			$rowset = $row->findManyToManyRowset('Catalog_Model_DbTable_Options',
												 'Catalog_Model_DbTable_HasOptions',
												null,null, $select)->toArray();
			$this->_setStatus(self::SUCCESS);
			return $rowset;
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			
			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
		}
	}
	
	
	/**
	 * @param integer|string|Zend_Db_Table_Abstract $row
	 * @return array
	 */
	public function findAllService($row) {
		if (is_int($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}
		
		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_setStatus(self::FAILURE);

			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
			return;
		}

		// HACK for serialized row
		if(!$row->isConnected()) {
			$row->setTable($this->getDbTable());
		}
		
		$select = $row->select()->order('name ASC');
		
		try {
			$rowset = $row->findManyToManyRowset(
							'Catalog_Model_DbTable_Service',
							'Catalog_Model_DbTable_HasService',
							null,null,$select
			);

			$this->_setStatus(self::SUCCESS);
			return $rowset->toArray();
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			
			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
		}
	}
	
	/**
	 * @param integer|string|Zend_Db_Table_Abstract $row
	 * @return array
	 */
	public function findAllStaff($row) {
		if (is_int($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}
		
		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_setStatus(self::FAILURE);
			return;
		}

		// HACK for serialized row
		if(!$row->isConnected()) {
			$row->setTable($this->getDbTable());
		}
		
		try {
			$rowset = $row->findDependentRowset('Catalog_Model_DbTable_Staff')->toArray();
			$this->_setStatus(self::SUCCESS);
			return $rowset;
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			
			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
		}
	}
	
	/**
	 * @param integer|string|Zend_Db_Table_Abstract $row
	 * @return array
	 */
	public function findAllImage($row) {
		if (is_int($row)) {
			$row = $this->findById($row, false);
		} else
		if (is_string($row)) {
			$row = $this->findByAlias($row, false);
		}
		
		if (!$row instanceof Zend_Db_Table_Row_Abstract) {
			$this->_setStatus(self::FAILURE);
			return;
		}

		// HACK for serialized row
		if(!$row->isConnected()) {
			$row->setTable($this->getDbTable());
		}

		try {
			$rowset = $row->findDependentRowset('Catalog_Model_DbTable_Image')->toArray();
			$this->_setStatus(self::SUCCESS);
			return $rowset;
		} catch (Zend_Db_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			
			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
		}
	}
	
	/**
	 * Fetch all records as:
	 * <code>
	 *	array(
	 *		primaryKey1 => name1,
	 *		...
	 *		primaryKeyn => namen
	 *  )
	 * </code>
	 * 
	 * @return array
	 */
	public function fetchAllAsPair() {
		$result = array();
		$table = $this->getDbTable();

		try {
			$rowset = $table->fetchAll();
		} catch(Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			
			// Nie cache-uj
			$this->_cacheSave = self::NO_CACHE;
			return;
		}
		
		foreach ($rowset as $row) {
			$result[$row->id] = $row->name;
		}

		$this->_setStatus(self::SUCCESS);
		return $result;
	}
}

/**
 * BACK COMPABILITY 
 */





require_once 'user/models/User.php';

require_once 'KontorX/Db/Table/Abstract.php';
class Catalog extends KontorX_Db_Table_Abstract {
    protected $_name = 'catalog';
    protected $_rowClass = 'Catalog_Row';

    protected $_dependentTables = array(
        'CatalogTime',
        'CatalogSite',
        'CatalogImage',
        'CatalogServiceCost',
        'CatalogPromoTime',
        'CatalogHasCatalogOptions',
        'CatalogStaff',
        'CatalogHasCatalogStaff'
    );

    protected $_referenceMap    = array(
        'CatalogDistrict' => array(
            'columns'           => 'catalog_district_id',
            'refTableClass'     => 'CatalogDistrict',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name',
            'onDelete'		=> self::CASCADE
        ),
        'CatalogImage' => array(
            'columns'           => 'catalog_image_id',
            'refTableClass'     => 'CatalogImage',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'image'
        ),
        'CatalogType' => array(
            'columns'           => 'catalog_type_id',
            'refTableClass'     => 'CatalogType',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name',
            'onDelete'          => self::CASCADE
        ),
        'CatalogOption1' => array(
            'columns'           => 'catalog_option1_id',
            'refTableClass'     => 'CatalogOptions',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
        'CatalogOption2' => array(
            'columns'           => 'catalog_option2_id',
            'refTableClass'     => 'CatalogOptions',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
        'CatalogOption3' => array(
            'columns'           => 'catalog_option3_id',
            'refTableClass'     => 'CatalogOptions',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'name'
        ),
        'User' => array(
            'columns'           => 'user_id',
            'refTableClass'     => 'User',
            'refColumns'        => 'id',
            'refColumnsAsName'  => 'username'
        )
    );

    /**
     * Zwraca zapytanie pobierajace rekordy promo plus
     *
     * @param KontorX_Db_Table_Tree_Row_Abstract $row
     * @return Zend_Db_Select
     */
    public function selectForListPromoPlus(KontorX_Db_Table_Tree_Row_Abstract $row = null) {
        require_once 'Zend/Db/Select.php';
        $select = new Zend_Db_Select($this->getAdapter());

        $select
        ->from(array('c' => 'catalog'),'*')
        ->join(array('cd' => 'catalog_district'),
            'cd.id = c.catalog_district_id',
            array('district_url' => 'cd.url',
                'district' => 'cd.name'))
        ->joinInner(array('cpt' => 'catalog_promo_time'),
            'c.id = cpt.catalog_id '.
            'AND NOW() BETWEEN cpt.t_start AND cpt.t_end',
            array('cpt.catalog_promo_type_id'))
        ->joinLeft(array('cs' => 'catalog_site'),
            'c.id = cs.catalog_id',
            array('cs.url'))

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

        /***/
        ->order('cpt.catalog_promo_type_id DESC')
        ->order('c.name ASC')
        ->where('cpt.catalog_promo_type_id = 3');	// tylko promocujne +

        // dodatkowy filtr na obszary - podobszary! bo promo!
        if (null !== $row) {
            $select->where('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
        }

        return $select;
    }

    /**
     * Zwraca zapytanie pobierające rekordy dal domyślnych rekordów
     * z sortowaniem rekordów promo (NIE promo plus)
     *
     * @param KontorX_Db_Table_Tree_Row_Abstract $row
     * @return Zend_Db_Select
     */
    public function selectForListDefault(KontorX_Db_Table_Tree_Row_Abstract $row = null) {
        require_once 'Zend/Db/Select.php';
        $select = new Zend_Db_Select($this->getAdapter());

        $select
        ->from(array('c' => 'catalog'),'*')
        ->join(array('cd' => 'catalog_district'),
            'cd.id = c.catalog_district_id',
            array('district_url' => 'cd.url',
                  'district' => 'cd.name'))
        ->joinLeft(array('cpt' => 'catalog_promo_time'),
            'c.id = cpt.catalog_id '.
            'AND cpt.catalog_promo_type_id <> 3 '. // bez promo +
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

        ->order('cpt.catalog_promo_type_id DESC');
//        ->order('c.name ASC');
        //			->where('cpt.catalog_promo_type_id < 3')
        //			->orWhere('cpt.catalog_promo_type_id = NULL'); // default i promo (NIE promocujne +) ??


        // dodatkowy filtr na obszary + podobszary
        if (null !== $row) {
            $db = $this->getAdapter();
//            $select->where('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
            try {
                $where = array();
                $where[] = $db->quoteInto('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
                foreach ($row->findChildrens() as $row) {
                    $where[] = $db->quoteInto('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
                }
                $select->where(implode(" OR ", $where));
            } catch (Exception $e) {
                Zend_Registry::get('logger')
                ->log($e->getMessage() ."\n".$e->getTraceAsString(), Zend_Log::ERR);
            }
        }

        return $select;
    }

     /**
     * Zwraca zapytanie pobierające rekordy dal domyślnych rekordów
     * z sortowaniem rekordów promo (NIE promo plus)
     *
     * @param arary $data
     * @return Zend_Db_Select
     */
    public function selectForSearch(array $data) {
        require_once 'Zend/Db/Select.php';
        $select = new Zend_Db_Select($this->getAdapter());

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
        ->group('c.id');

        // tworzenie filtrow ;]
        
//        var_dump($data);

        $db = $this->getAdapter();
        
        $data = array_merge(array(
        	'name' => null,
        	'street' => null,
        	'district' => null,
        	'service' => null,
        	'options' => null,
        	'hour' => null,
        	'week' => 0,
		), $data);

//var_dump($data);
//die(1);
        // nazwa
        if (isset($data['name'])) {
        	// wyszukiwana nazwa co najmniej 3 znaki
        	if (strlen($data['name']) > 2) {
			foreach(explode(' ', (string) $data['name']) as $name) {
				$select->orWhere('c.name LIKE ?', '%'.$name.'%');
			}
        	}
        }

        // ulica
        if (isset($data['street'])) {
        	// nazwa ulicy co najmniej 3 znaki
        	if (strlen($data['street']) > 2) {
        		$select->orWhere('c.adress LIKE ?', '%'.((string)$data['street']).'%');
        	}
        }

        // obszary
        if (isset($data['district'])) {
        	if (is_array($data['district'])) {
        		$data['district'] = current($data['district']);
        	}

	        if (is_numeric($data['district'])) {
	            $catalogDistrict = new CatalogDistrict();
	            try {
	                $row = $catalogDistrict->fetchRow(
	                    $catalogDistrict->select()->where('id = ?', $data['district'], Zend_Db::INT_TYPE)
	                );
	
	                if ($row instanceof KontorX_Db_Table_Tree_Row_Abstract) {
	                    $where = array();
	                    $where[] = $db->quoteInto('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
	                    try {
	                        foreach ($row->findChildrens() as $row) {
	                            $where[] = $db->quoteInto('c.catalog_district_id = ?', $row->id, Zend_Db::INT_TYPE);
	                        }
	                        $select->where(implode(" OR ", $where));
	                    } catch (Exception $e) {
	                        Zend_Registry::get('logger')
	                        ->log($e->getMessage() ."\n".$e->getTraceAsString(), Zend_Log::ERR);
	                    }
	                }
	            } catch (Zend_Db_Table_Abstract $e) {
	                Zend_Registry::get('logger')
	                ->log($e->getMessage() ."\n".$e->getTraceAsString(), Zend_Log::ERR);
	            }
	        }
        }

        // services
        if (isset($data['service'])) {
	        if (count($data['service']) > 0) {
	            $where = array();
	            foreach ((array) $data['service'] as $serviceId) {
	                if (is_numeric($serviceId)) {
	                    $where[] = 'csc.catalog_service_id = ' . (int) $serviceId;
	                }
	            }
	            if (count($where)) {
	                $where = implode(" AND ", $where);
	                $select->joinLeft(array('csc' => 'catalog_service_cost'),
	                       'c.id = csc.catalog_id', array());
	                $select->where($where);
	            }
	        }
        }
        

        // opcje
        if (isset($data['options'])) {
	        if (count($data['options']) > 0) {
//	        	var_dump($data['options']);
	            $where = array();
	            foreach ((array) $data['options'] as $optionId) {
	                if (is_numeric($optionId)) {
	                    $where[] = 'chco.catalog_options_id = ' . (int) $optionId;
	                }
	            }
	            if (count($where)) {
	                $where = implode(" AND ", $where);
	                $select->joinLeft(array('chco' => 'catalog_has_catalog_options'),
	                       'c.id = chco.catalog_id', array());
	                $select->where($where);
	            }
	        }
        }

        // opcje
        if (@$data['hour'] != '' || count(@$data['week']) > 0) {
            // dzien i godzina
            if (@$data['hour'] != '' && $data['week'] > 0) {
                $week = ((int)$data['week'])-2;
                $weekName = strtolower(date("l",mktime(0,0,0,0,$week,0,0)));
                $start = "ct.{$weekName}_start";
                $end   = "ct.{$weekName}_end";

                $hour = explode(":", $data['hour']);
                $hour = array_merge($hour, array_fill(0, 2, "00"));
                array_splice($hour, 2, 3);
                $hour = implode(":", $hour);

                $select
                    ->joinLeft(array('ct' => 'catalog_time'),
                        'c.id = ct.catalog_id', array())
                    ->where("TIME(?) BETWEEN $start AND $end", $hour);
            } else {
                // godzina
                if (@$data['hour'] != '') {
                    $hour = explode(":", $data['hour']);
                    $hour = array_merge($hour, array_fill(0, 2, "00"));
                    array_splice($hour, 2, 3);
                    $hour = implode(":", $hour);
                    
                    $where = array();
                    for ($i=0;$i<=7;$i++) {
                        $weekName = strtolower(date("l",mktime(0,0,0,0,$i,0,0)));
                        $start = "ct.{$weekName}_start";
                        $end   = "ct.{$weekName}_end";

                        $where[] = $db->quoteInto("TIME(?) BETWEEN $start AND $end", $hour);
                    }

                    $select
                        ->joinLeft(array('ct' => 'catalog_time'),
                            'c.id = ct.catalog_id', array())
                        ->where(implode(" OR ", $where));
                } else
                // dzien
                if ($data['week'] > 0) {
                    $week = ((int)$data['week'])-2;
                    $weekName = strtolower(date("l",mktime(0,0,0,0,$week,0,0)));
                    $start = "ct.{$weekName}_start";
                    $end   = "ct.{$weekName}_end";

                    $select->joinLeft(array('ct' => 'catalog_time'),
                   'c.id = ct.catalog_id', array());
                    $select->where("$start > '00:00:00' AND $end > '00:00:00'");
                }
            }
        }

        return $select;
    }
}

require_once 'Zend/Db/Table/Row/Abstract.php';
class Catalog_Row extends Zend_Db_Table_Row_Abstract {
    public function findNearRowset(Zend_Db_Select $select = null) {
        $table = $this->getTable();
        if (null === $select) {
            $select = $table->select();
        }

        $select
        // szukamy w tej samej dzielnicy
        ->where('catalog_district_id = ?', $this->catalog_district_id)
        ->where('lng AND lat <> 0')
        ->order('lng ASC')
        ->order('lat ASC');

        return $table->fetchAll($select);
    }
}
