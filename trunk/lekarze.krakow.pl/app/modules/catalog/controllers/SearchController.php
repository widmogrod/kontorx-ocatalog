<?php
require_once 'KontorX/Controller/Action.php';
class Catalog_SearchController extends KontorX_Controller_Action {

    public $skin = array(
		'layout' => 'catalog_search',
		'generate' => array(
    		'layout' => 'admin_catalog',
    		'config' => array(
    			'filename' => 'backend_config.ini'),
    	)
    );

    public $contexts = array(
    	'semantic' => array('opensearch')
    );
    
    public $ajaxable = array(
    	'semantic' => array('html')
    );
    
    public function init() {
		parent::init();

		$configMain = $this->_helper->config('config.ini');
		$this->view->apiKey = $configMain->gmap->{BOOTSTRAP}->apiKey;

		/* @var $ajaxContext Zend_Controller_Action_Helper_AjaxContext */
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->initContext('html');

		/* @var $contextSwitch Zend_Controller_Action_Helper_ContextSwitch */
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		if (!$contextSwitch->hasContext('opensearch')) {
			$contextSwitch->addContext('opensearch', array(
				'suffix' => 'opensearch'
			));
		}
		$contextSwitch->initContext();
    }
    
    /**
     * Domyslna akcja
     */
    public function indexAction() {
    	$this->_forward('advanced');
    }

    /**
     * Wyszukieanie zaawansowane
     * @todo dodac cache! - np. catalog_district i sprawdzanie czy istnieje .. czyszczone gdy zostanie dodana nowa dzielnica
     * @todo optymalizować
     */
    public function advancedAction() {
        $config = $this->_helper->config('index.xml');

        $rq = $this->getRequest();
        $data = $rq->getParams();

        $f = new KontorX_Filter_MagicQuotes();
        $data = $f->filter($data);
        $this->view->input = $data;

        $this->_setupSearchView();

        $catalog = new Catalog();

        /**
         * Set cache for @see KontorX_DataGrid_Adapter_Abstract
         * and for @see Zend_Paginator
         */
        $cache = Zend_Registry::get('cacheDBQuery');
        KontorX_DataGrid_Adapter_Abstract::setCache($cache);
        Zend_Paginator::setCache($cache);

        // rekordy all
        $select = $catalog->selectForSearch($data);
        $grid = KontorX_DataGrid::factory($select, $config->dataGrid);
        $this->view->grid = $grid;

        $page = $this->_getParam('page');
        $onPage = 15;

        $paginator = Zend_Paginator::factory($select);
        $grid->setPaginator($paginator);
        $grid->setPagination($page, $onPage);
    }
    
    /**
     * Wyszukiwanie semantyczne!
     * @return void
     */
    public function semanticAction() {
    	// Ustaw widok
    	$this->_setupSearchView();
    	
    	$rq = $this->getRequest();
    	$q = $rq->getParam('q');
        $data = $rq->getParams();
        $this->view->queryValues = http_build_query(array_merge(
        	$rq->getQuery(),
        	$rq->getPost()
        ));
//        var_dump($this->view->queryValues);

//        if (empty($q) && empty($data)) {
//        	return;
//        }
        
        // filtrowanie
        $f = new Zend_Filter();
        $f->addFilter(new KontorX_Filter_MagicQuotes());
        $f->addFilter(new Zend_Filter_StringTrim());
        $f->addFilter(new Zend_Filter_StripNewlines());
        $f->addFilter(new Zend_Filter_StripTags());
        
        $q = $f->filter($q);
        
        $f = new KontorX_Filter_MagicQuotes();
        $data = $f->filter($data);
        
        if (empty($q)) {
        	return;
        }

    	// przekazanie danych do widoku
    	$this->view->q = $q;
    	$this->view->placeholder('search')->q = $q;

    	// konfiguracja wyszukiwania
    	$searchConfig = $this->_helper->config('search.xml');

    	if (!$rq->isXmlHttpRequest()) {
    		// zrozum teraz o co mu chodzi!.. ;)
	    	$context = new KontorX_Search_Semantic_Context($q);
	    	$semantic = new KontorX_Search_Semantic($searchConfig->semantic);
	    	$semantic->interpret($context);

	    	$data['name'] = $context->getInput();
	    	$data = array_merge($data, $context->getOutput());
    	}

//    	var_dump($data);
    	
    	$this->view->input = $data;

    	/**
    	 * .. 
    	 */
    	/*$aspect = array(
    		'semantic' => array(
    			'before' => array(
    				array(KONTORX_ACTION, '_setupSearchView'),
    				array('CacheModel','initDataGridAndPaginator')
    			)
    		)
    	);*/

    	$config = $this->_helper->config('index.xml');
    	
    	// Ustawienie cachowania
    	$cache = Zend_Registry::get('cacheDBQuery');
//        KontorX_DataGrid_Adapter_Abstract::setCache($cache);
        Zend_Paginator::setCache($cache);

        $config = $this->_helper->config('list.ini');

		$page = $this->_getParam('page', 1);
		$rowCount = $config->rowCount;

        require_once 'catalog/models/Catalog.php';
        $catalog = new Catalog();
        
        // rekordy all
        $select = $catalog->selectForSearch($data);
        $select->limitPage($page, $rowCount);
        
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($rowCount);
        $this->view->paginator = $paginator;

        /* @var Zend_Cache_Core */
        $cacheId = md5((string)$select);
        if (false === ($rowset = $cache->load($cacheId))) {
        	try {
	        	/* @var Zend_Db_Statement_Interface */
		        $stmt = $select->query();
		        $rowset = $stmt->fetchAll();
		        $cache->save($rowset, $cacheId);
	        } catch (Zend_Db_Statement_Exception $e) {
	        	Zend_Registry::get('logger')
	        	->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);
	        }
        }
        
        $this->view->rowset = $rowset;
    }

    /**
     * Przekazanie do widoku, opcji, usług, obszarów
     * @return void
     */
    private function _setupSearchView() {
    	require_once 'catalog/models/CatalogDistrict.php';
    	$district = new CatalogDistrict();
        $this->view->districtRowset = $district->fetchAllCache();

        require_once 'catalog/models/CatalogOptions.php';
        $options = new CatalogOptions();
        $this->view->optionsArray = $options->fetchAllOptionsArrayCache();

        require_once 'catalog/models/CatalogService.php';
        $service = new CatalogService();
        $this->view->serviceArray = $service->fetchAllOptionsArrayCache();
    }
    
    /**
     * Strona główna zarządzania, wyszukiwaniem
     * @return void
     */
    public function adminAction() {
    	$this->_forward('generate');
    }
    
    /**
     * Generuje dane do semantycznego wyszukiwania
     * @return void
     */
    public function generateAction() {
    	require_once 'catalog/models/CatalogOptions.php';
    	$options = new CatalogOptions();

    	$optionsArray = $options->fetchAllArrayKeyValueExsists();
    	$optionsXml = KontorX_Config_Generate::factory($optionsArray, KontorX_Config_Generate::XML);
    	$this->view->optionsXml = $optionsXml;    	
    	
    	require_once 'catalog/models/CatalogService.php';
    	$service = new CatalogService();
    	$serviceArray = $service->fetchAllArrayKeyValueExsists();
    	$serviceXml = KontorX_Config_Generate::factory($serviceArray, KontorX_Config_Generate::XML);
    	$this->view->serviceXml = $serviceXml;
    	
    	require_once 'catalog/models/CatalogDistrict.php';
    	$district = new CatalogDistrict();
    	$districtArray = $district->fetchAllArrayKeyValueExsists();
    	$districtXml = KontorX_Config_Generate::factory($districtArray, KontorX_Config_Generate::XML);
    	$this->view->districtXml = $districtXml;
    }
}