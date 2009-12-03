<?php
class Catalog_Model_Search extends Promotor_Model_Abstract {
	
	const SEARCH = 'SEARCH';
	const FIND = 'FIND';
	const QUERY = 'QUERY';
	
	protected $_dbTable = array();
	
	protected $_cachedMethods = array(
		'findDefault'
	);
	
	/**
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable($type = null) {
		if (!array_key_exists($type, $this->_dbTable)) {
			switch ($type) {
				default: $type = self::SEARCH;

				case self::SEARCH: 	
					$this->_dbTable[$type] = new Catalog_Model_DbTable_Searching(); break;
				case self::FIND: 	
					$this->_dbTable[$type] = new Catalog_Model_DbTable_SearchFind(); break;
				case self::QUERY:	
					$this->_dbTable[$type] = new Catalog_Model_DbTable_SearchQuery(); break;
			}
		}
		return $this->_dbTable[$type];
	}

	/**
	 * @var Zend_Filter
	 */
	protected $_filter;
	
	public function filterQuery($value) {
		if (null === $this->_filter) {
	        $this->_filter = new Zend_Filter();
	        $this->_filter->addFilter(new KontorX_Filter_MagicQuotes());
	        $this->_filter->addFilter(new Zend_Filter_StringTrim());
	        $this->_filter->addFilter(new Zend_Filter_StripNewlines());
	        $this->_filter->addFilter(new Zend_Filter_StripTags());
		}
		return $this->_filter->filter($value);
	}

	public function addSearchQuery($query) {
		$row = $this->getDbTable(self::QUERY)->createRow(array('query' => $query));
		
		try {
			$row->save();
		} catch (Exception $e) {
			$this->_logException($e);
		}
	}
	
	/**
	 * @param string $query
	 * @param integer $page
	 * @param integer $rowCount
	 * @return array() 
	 */
	public function findDefault($query, $page, $rowCount) {
		$table = $this->getDbTable(self::SEARCH);
		$db = $table->getAdapter();

		$row = $table->fetchRow(
								$db->quoteInto('query = ?', strtolower($query)));
		if (null == $row) {
			return;
		}

		$queryId = $row->id;

		$select = new Zend_Db_Select($db);  
		$select
			->distinct(true)
	        ->from(array('c' => 'catalog'),'*')
	        ->where('c.publicated = 1')
	        
	        ->joinInner(array('csf' => 'catalog_search_find'),
	        			'c.id = csf.catalog_id AND csf.query_search_id = ' . (int) $queryId, array('query_idx' => 'idx'))
	        
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

	        ->order('query_idx DESC')
	        ->order('cpt.catalog_promo_type_id DESC')
	        
	        ->limitPage($page, $rowCount);

		try {
			$stmt = $select->query(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return array($rowset, $select);
		} catch (Exception $e) {
			$this->_logException($e);
		}
	}
	
	/**
	 * @param string $query
	 * @param integer $page
	 * @param integer $rowCount
	 * @param Zend_Confog|array $config
	 * @return array() 
	 */
	public function findSemantic($query, $page, $rowCount, $config) {
		// !@! debugowanie
		$this->_log($query, Zend_Log::DEBUG);

		// przygotowanie konfiguracji
		$config = $this->_prepareSemanticSearchConfig($config);

        // zrozum teraz o co mu chodzi!.. ;)
    	$context = new KontorX_Search_Semantic_Context($query);
    	$configemantic = new KontorX_Search_Semantic($config);
    	$configemantic->interpret($context);

    	// tworzenie tablicy danych potrzebnych do skonstruowania zapytania select
    	$data = array('name' => $context->getInput());
    	$data = array_merge($data, $context->getOutput());

    	$select = $this->_selectSemantic($data)
    				   ->limitPage($page, $rowCount);

		try {
			$stmt = $select->query(Zend_Db::FETCH_ASSOC);
			$rowset = $stmt->fetchAll();

			return array($rowset, $select);
		} catch (Exception $e) {
			$this->_logException($e);
		}
	}

	/**
	 * @param Zend_Confog|array $config
	 * @return array
	 * 
	 * @todo Cachowanie
	 */
	protected function _prepareSemanticSearchConfig($config) {
		// konfiguracja wyszukiwania
        $config = (is_array($config)) ? $config : $config->toArray();

		$modelOptions = new Catalog_Model_Options();
        $arrOptions   = $modelOptions->fetchAllArrayKeyValueExsistsCache();

        $modelService = new Catalog_Model_Service();
        $arrService   = $modelService->fetchAllArrayKeyValueExsistsCache();
        
        $modelType = new Catalog_Model_Type();
        $arrType   = $modelType->fetchAllArrayKeyValueExsistsCache();

        $modelDistrict = new Catalog_Model_District();
        $arrDistrict   = $modelDistrict->fetchAllArrayKeyValueExsistsCache();

        // options
    	$config['semantic']['interpreters']['options']['options']['interpreters']['serviceContext']['options']['interpreters']['options']['options'] = array(
    		'separatorRequired' => 0
    	);
    	$config['semantic']['interpreters']['options']['options']['interpreters']['serviceContext']['options']['interpreters']['options']['options']
    		+= $arrOptions;
    	
    	$config['semantic']['interpreters']['options']['options']['interpreters']['options']['options'] = array(
    		'multi' => 1
    	);
    	$config['semantic']['interpreters']['options']['options']['interpreters']['options']['options'] += $arrOptions;
    	
    	// service
    	$config['semantic']['interpreters']['service']['options']['interpreters']['serviceContext']['options']['interpreters']['service']['options'] = array(
    		'separatorRequired' => 0
    	);
    	$config['semantic']['interpreters']['service']['options']['interpreters']['serviceContext']['options']['interpreters']['service']['options']
    		+= $arrService;
    	
    	$config['semantic']['interpreters']['service']['options']['interpreters']['service']['options'] = array(
    		'multi' => 1
    	);
    	$config['semantic']['interpreters']['service']['options']['interpreters']['service']['options'] += $arrService;
    	
    	// type
    	$config['semantic']['interpreters']['type']['options']['interpreters']['serviceContext']['options']['interpreters']['type']['options'] = array(
    		'separatorRequired' => 0
    	);
    	$config['semantic']['interpreters']['type']['options']['interpreters']['serviceContext']['options']['interpreters']['type']['options']
    		+= $arrType;
    	
    	$config['semantic']['interpreters']['type']['options']['interpreters']['type']['options'] = array(
    		'multi' => 0
    	);
    	$config['semantic']['interpreters']['type']['options']['interpreters']['type']['options'] += $arrType;

    	// district
    	$config['semantic']['interpreters']['district']['options'] = array(
        	'multi' => 1
        );
        $config['semantic']['interpreters']['district']['options'] 
        	+= $arrDistrict;

        return $config['semantic'];
	}
	
	protected function _selectSemantic(array $data) {
		$table = $this->getDbTable(self::FIND);
		$db = $table->getAdapter();

		$select = new Zend_Db_Select($db);

        $select
        	->distinct(true)
	        ->from(array('c' => 'catalog'),'*')
	        ->where('c.publicated = 1')

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
	        ->order('c.idx DESC')

	        ->group('c.id');

	    // !@! debugowanie
		$this->_log(print_r($data, true), Zend_Log::DEBUG);
	        
	        
        // żeby pominąć zbędne funkcje isset itp..
        $data = array_merge(array(
        	'name' => null,
        	'street' => null,
        	'district' => null,
        	'service' => null,
        	'options' => null,
        	'hour' => null,
        	'type' => null,
        	'week' => 0,
		), $data);

		/**
		 * To filtrujemy i szukamy.. 
		 */
		
        // szukaj w nazwie wizytówki, gdy co najmniej 1 znak
        if (strlen($data['name']) > 0) {
			foreach(explode(' ', (string) $data['name']) as $name) {
				$select->where('c.name LIKE ?', '%'.$name.'%');
			}
        }

        // szukaj po ulicy, gdy co najmniej 3 znaki
        if (strlen($data['street']) > 2) {
        	$select->where('c.adress LIKE ?', '%'.((string)$data['street']).'%');
        }

        // szukaj po dzielnicy, gdy dopasowano przynajminej jedną
        if (count($data['district']) > 0) {
        	// usuwa zduplokowane wartości (TIP: bo klucze mogą być tylko unikalne ..)
        	$data['district'] = array_flip(array_flip($data['district']));

        	$where = array();
        	foreach((array) $data['district'] as $id) {
        		$this->_log($id);
        		$where[] = $db->quoteInto('c.catalog_district_id = ?', (int) $id);
        	}

        	$select->where(implode(' OR ', $where));
        }

        // szukaj po usługach
        $isSelectForService = false; 
        if (count($data['service']) > 0) {
        	// usuwa zduplokowane wartości (TIP: bo klucze mogą być tylko unikalne ..)
        	$data['service'] = array_flip(array_flip($data['service']));

            $where = array();
            foreach ((array) $data['service'] as $serviceId) {
                if (is_numeric($serviceId)) {
                    $where[] = 'csc.catalog_service_id = ' . (int) $serviceId;
                }
            }
            if (count($where)) {
            	$isSelectForService = true;

                $where = implode(" OR ", $where);
                $select->joinLeft(array('csc' => 'catalog_service_cost'),
                       'c.id = csc.catalog_id', array());
                $select->where($where);
            }
        }

        // szukaj po gabinet oferuje
        if (count($data['options']) > 0) {
        	// usuwa zduplokowane wartości (TIP: bo klucze mogą być tylko unikalne ..)
        	$data['options'] = array_flip(array_flip($data['options']));
        	
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

                if ($isSelectForService) {
                	// TEST: jeżeli istnieje usługa to też uwzględnij opcje 
                	$select->orWhere($where);
                } else {
                	$select->where($where);
                }
            }
        }
        
		// szukaj po usługach
        if (null !== $data['type']) {
			$select->where('c.catalog_type_id = ?', $data['type']);
        }

        // szukaj po godzinie otwarcia
        if (null !== $data['hour'] || $data['week'] > 0) {
            // dzien i godzina
            if ($data['hour'] != '' && $data['week'] > 0) {
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

        // !@! debugowanie
		$this->_log((string) clone $select, Zend_Log::DEBUG);
        
        return $select;
	}
	
	/**
	 * @param Exception $e
	 * @param int $type
	 * @return void
	 */
	public function _logException(Exception $e, $type = null) {
		$message = sprintf('%s :: %s (%d) %s', get_class($e), $e->getMessage(), $e->getLine(), basename($e->getFile()));
		if (null === $type) {
			$type = Zend_Log::CRIT;
		}
		Zend_Registry::get('logger')->log($message, $type);
	}
	
	/**
	 * @param string $message
	 * @param int $type
	 * @return void
	 */
	public function _log($message, $type = null) {
		if (null === $type) {
			$type = Zend_Log::CRIT;
		}
		Zend_Registry::get('logger')->log($message, $type);
	}
}