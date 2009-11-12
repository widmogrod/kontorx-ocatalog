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

	        ->order('query_idx ASC')
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
	
	public function findSemantic($query, $config) {
//		// konfiguracja wyszukiwania
//    	$searchConfig = $this->_helper->config('search.xml');
//        $s = $searchConfig->toArray();
//
//        	print '<pre>';
////        	print_r($s);
//
//        	require_once 'catalog/models/CatalogOptions.php';
//	    	$options = new CatalogOptions();
//	    	$optionsArray = $options->fetchAllArrayKeyValueExsists();
//	    	
//	    	require_once 'catalog/models/CatalogService.php';
//	    	$service = new CatalogService();
//	    	$serviceArray = $service->fetchAllArrayKeyValueExsists();
//	    	
//	    	require_once 'catalog/models/CatalogDistrict.php';
//	    	$district = new CatalogDistrict();
//	    	$districtArray = $district->fetchAllArrayKeyValueExsists();
//        	
//
//	    	$s['semantic']['interpreters']['options']['options']['interpreters']['serviceContext']['options']['interpreters']['options']['options'] = array(
//	    		'separatorRequired' => 0
//	    	);
//	    	$s['semantic']['interpreters']['options']['options']['interpreters']['serviceContext']['options']['interpreters']['options']['options']
//	    		+= $optionsArray;
//	    	
//	    	$s['semantic']['interpreters']['options']['options']['interpreters']['options']['options'] = array(
//	    		'multi' => 1
//	    	);
//	    	$s['semantic']['interpreters']['options']['options']['interpreters']['options']['options'] += $optionsArray;
////	    	unset($s['semantic']['interpreters']['options']);
//	    	
//	    	$s['semantic']['interpreters']['service']['options']['interpreters']['serviceContext']['options']['interpreters']['service']['options'] = array(
//	    		'separatorRequired' => 0
//	    	);
//	    	$s['semantic']['interpreters']['service']['options']['interpreters']['serviceContext']['options']['interpreters']['service']['options']
//	    		+= $serviceArray;
//	    	
//	    	$s['semantic']['interpreters']['service']['options']['interpreters']['service']['options'] = array(
//	    		'multi' => 1
//	    	);
//	    	$s['semantic']['interpreters']['service']['options']['interpreters']['service']['options'] += $serviceArray;
////	    	unset($s['semantic']['interpreters']['service']);
//
////	    	var_dump($optionsArray);
////	    	var_dump($serviceArray);
////	    	var_dump($districtArray);
//	    	
//	    	$s['semantic']['interpreters']['district']['options'] = array(
//        		'multi' => 1
//        	);
//        	$s['semantic']['interpreters']['district']['options'] += $districtArray;
//        	
////        	unset($s['semantic']['interpreters']['district']);
//	    	
////        	$s['semantic']['interpreters']['optionss'] = Array(
////                            'interpreter' => 'ArrayKeyLikeExsists',
////                            'name' => 'optionss',
////                            'options' => $serviceArray);
//        	
//        	
////        	print_r($s);
//        	
//        	
//        	// zrozum teraz o co mu chodzi!.. ;)
//	    	$context = new KontorX_Search_Semantic_Context($q);
//	    	$semantic = new KontorX_Search_Semantic((array) @$s['semantic']);
//	    	$semantic->interpret($context);
//
//	    	$data['name'] = $context->getInput();
//	    	$data = array_merge($data, $context->getOutput());
	}
	
	/**
	 * @param Exception $e
	 * @param int $type
	 * @return void
	 */
	public function _logException(Exception $e, $type = null) {
		$message = sprintf('%s :: %s', get_class($e), $e->getMessage());
		if (null === $type) {
			$type = Zend_Log::CRIT;
		}
		Zend_Registry::get('logger')->log($message, $type);
	}
}