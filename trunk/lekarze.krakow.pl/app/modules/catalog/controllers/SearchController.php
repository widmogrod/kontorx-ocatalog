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
     * 
     * @todo aktywować metode modelu $model->findSemantic
     * @todo dodać cache metody $model->findSemantic
     * @todo dodać w PA odpowiednie pola do przełanczania się pomiędzy akcjami wyszukiwania
     * @todo dodać Panel Reklamy OpenX
     * @todo dodać pozycjonowanie wizytówek! w tabeli premium.. lub może lepiej w catalog
     */
    public function semanticAction() {
    	$model = new Catalog_Model_Search();

    	/* @var Zend_Controller_Request_Http */
    	$rq = $this->getRequest();

    	$query = $rq->getParam('q');
    	$query = $model->filterQuery($query);
    	
    	if (empty($query)) {
    		// koniec zabawy
        	return;
        }

	    	// przekazanie danych do widoku
	    	$this->view->q = $query;
	    	$this->view->placeholder('search')->q = $query;

	    // ustawiania dla stronicowania
	    $page = $rq->getParam('page', 1);
	    $rowCount = $this->_helper->config('list.ini')->rowCount;

	    // odbespiecznie danych.. ;)	
        $filter = new KontorX_Filter_MagicQuotes();
        $data = $filter->filter($rq->getParams());

        // zapisz szukane frazy
        $model->addSearchQuery($query);

        if (null === ($result = $model->findDefaultCache($query, $page, $rowCount))) {
        	// szukanie semantyczne
        	$config = $this->_helper->config('search.xml');
        	if (null === ($result = $model->findSemantic($query, $page, $rowCount, $config))) {
        		// brak wyników
        		return;
        	}
        }

        @list($rowset, $select) = $result;

        $this->view->rowset = $rowset;

        try {
        	$paginator = Zend_Paginator::factory($select);
	        $paginator->setCurrentPageNumber($page);
	        $paginator->setItemCountPerPage($rowCount);
	        $this->view->paginator = $paginator;
        } catch (Exception $e) {
        	$model->_logException($e);
        }
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