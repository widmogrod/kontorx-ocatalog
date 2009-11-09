<?php
require_once 'KontorX/Controller/Action.php';
class Catalog_IndexController extends KontorX_Controller_Action {

    public $skin = array(
		'layout' => 'catalog',
		'show' => array(
			'layout' => 'catalog_show',
			'lock' => true
        ),
		'search' => array(
			'layout' => 'catalog_full',
			'lock' => true
        )
    );

    public $cache = array(
//            		'index' => array('id' => 'params'),
        //    		'az' => array('id' => array('param' => 'string')),
        //    		'show' => array('id' => array('param' => 'id'))
    );

    public $contexts = array(
        'mapdata' => array('json'),
        'map' => array('html')
    );

    public function init() {
        parent::init();

        $contextSwitch = $this->_helper->getHelper('ContextSwitch');
        if (!$contextSwitch->hasContext('html')) {
            $contextSwitch
            ->addContext('html', array(
                'suffix' => 'html',
                'headers'   => array('Content-Type' => 'text/html'),
                ));
        }
        $contextSwitch
        ->setAutoJsonSerialization(false)
        ->initContext();

        $configMain = $this->_helper->config('config.ini');
        $this->view->apiKey = $configMain->gmap->{BOOTSTRAP}->apiKey;
    }

    /**
     * Strona główna
     * @todo dodać ograniczenia programowe dla rekordow promo itd.
     * @todo cache paginacji
     */
    public function indexAction() {
//        $config = $this->_helper->config('index.xml');
//
//        require_once 'catalog/models/Catalog.php';
//        $catalog = new Catalog();
//
//        /**
//         * Set cache for @see KontorX_DataGrid_Adapter_Abstract
//         * and for @see Zend_Paginator
//         */
//        $cache = Zend_Registry::get('cacheDBQuery');
//        KontorX_DataGrid_Adapter_Abstract::setCache($cache);
//        Zend_Paginator::setCache($cache);
//
//        // rekordy promocyjne
//        $select = $catalog->selectForListPromoPlus();
//        $gridPromo = KontorX_DataGrid::factory($select, $config->dataGrid);
//        $this->view->gridPromo = $gridPromo;
//
//        // rekordy zwykle
//        $select = $catalog->selectForListDefault();
//        $grid = KontorX_DataGrid::factory($select, $config->dataGrid);
//        $this->view->grid = $grid;
//
//        $page = $this->_getParam('page');
//        $onPage = 15;
//
//        $paginator = Zend_Paginator::factory($select);        
//        $grid->setPaginator($paginator);
//        $grid->setPagination($page, $onPage);
    }

    /**
     * Strona wyswietlania kategorii
     * @todo dodać ograniczenia programowe dla rekordow promo itd.
     * @todo cache paginacji
     */
    public function categoryAction() {
        $config = $this->_helper->config('index.xml');

        $categoryUrl = $this->_getParam('url', $config->default->category->url);
        $this->view->categoryUrl = $categoryUrl;

        require_once 'catalog/models/CatalogDistrict.php';
        $catalogDistrict = new CatalogDistrict();

        // sprawdzanie czy istnieje kategoria
        $select = $catalogDistrict->select();
        $select
        ->where('url = ?', $categoryUrl);

        try {
            $row = $catalogDistrict->fetchRow($select);
        } catch (Zend_Db_Table_Abstract $e) {
            // logowanie wyjatku
            $logger = Zend_Registry::get('logger');
            $logger->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

            $row = null;
        }

        // czy aby napewno ..
        if (!$row instanceof KontorX_Db_Table_Tree_Row_Abstract) {
            $this->_helper->viewRenderer->render('category.no.exsists');
            return;
        }

        $this->view->categoryRow = $row;

        require_once 'catalog/models/Catalog.php';
        $catalog = new Catalog();

        /**
         * Set cache for @see KontorX_DataGrid_Adapter_Abstract
         * and for @see Zend_Paginator
         */
        $cache = Zend_Registry::get('cacheDBQuery');
        KontorX_DataGrid_Adapter_Abstract::setCache($cache);
        Zend_Paginator::setCache($cache);

        // rekordy promocyjne
        $select = $catalog->selectForListPromoPlus($row);
        $gridPromo = KontorX_DataGrid::factory($select, $config->dataGrid);
        $this->view->gridPromo = $gridPromo;

        // rekordy zwykle
        $select = $catalog->selectForListDefault($row);
        $grid = KontorX_DataGrid::factory($select, $config->dataGrid);
        $this->view->grid = $grid;

        $page = $this->_getParam('page');
        $onPage = 15;

        $paginator = Zend_Paginator::factory($select);
        $grid->setPaginator($paginator);
        $grid->setPagination($page, $onPage);
    }

    /**
     * Feedback o błędzie w wizytówce
     * @todo stworzyć by był bardziej akcja globalna nie modulową
     */
    public function errorAction() {
        $config = $this->_helper->config('index.xml');

        require_once 'Zend/Form.php';
        $form = new Zend_Form($config->forms->error);

        require_once 'KontorX/Observable/Form.php';
        $observable = new KontorX_Observable_Form($form);

        require_once 'catalog/models/Observer/Form.php';
        $formObserver = new Catalog_Observer_Form(
            Catalog_Observer_Form::ERROR_NOTICE,
            $config->config->error
        );
        $observable->addObserver($formObserver);

        $request = $this->getRequest();

        if (!$request->isPost()) {
            $form->setDefaults($this->_getErrorFormDefaultValues());
            $this->view->form = $form;
            return;
        }

        try {
            if (!$observable->isValid($request->getPost())) {
                $this->view->form = $form;
                return;
            }
        } catch (KontorX_Observable_Exception $e) {
            Zend_Registry::get('logger')
            ->log($e->getMessage() ."\n". $e->getTraceAsString(),Zend_Log::ERR);
        }

        $message = "Komunikat o błędzie został wysłany, dziękujemy!";
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector->goToUrlAndExit(
            $form->getValue('referer')
        );
    }

    private function _getErrorFormDefaultValues() {
        $referer = getenv("HTTP_REFERER");
        if (null === $referer) {
            $referer = $this->_getParam('id');
        }
        return array(
            'referer' => $referer,
            'message' => "Zostały znalezione następujące błędy:\n".
                         " 1. \n".
                         " 2. \n".
                         " 3. \n"
        );
    }

    /**
     * Dane dla mapy OFF
     */
    /*
    public function mapdataAction() {
        require_once 'catalog/models/Catalog.php';
        $catalog = new Catalog();

        try {
            $data   = $catalog->fetchAllForMap();
            $format = $this->_getParam('format','json');
            $catalog->saveCacheMapData($data, $format, PUBLIC_PATHNAME);

            $this->view->data = $data;
        } catch (Zend_Db_Table_Exception $e) {
            Zend_Registry::get('logger')
            ->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

            $this->_helper->viewRenderer->render('mapdata.error');
        }
    }

    public function mapAction() {
        $id = $this->view->id = (int) $this->_getParam('id');
        if ($id > 0) {
            require_once 'catalog/models/Catalog.php';
            $catalog = new Catalog();

            try {
                $this->view->row = $catalog->find($id)->current();
            } catch (Zend_Db_Table_Abstract $e) {
                Zend_Registry::get('logger')
                ->log($e->getMessage() ."\n".$e->getTraceAsString(), Zend_Log::ERR);
            }
        }
    }
    public function nearAction() {
        // wylanczamy layout
        $this->_helper->layout->disableLayout();

        $catalogId = $this->_getParam('id');

        require_once 'catalog/models/Catalog.php';
        $model = new Catalog();

        $select = $model->select()
        ->where('id = ?', $catalogId, Zend_Db::INT_TYPE);

        try {
            $row = $model->fetchRow($select);
        } catch (Zend_Db_Table_Abstract $e) {
            // logowanie wyjatku
            Zend_Registry::get('logger')
            ->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);

            $row = null;
        }

        // czy aby napewno ..
        if (!$row instanceof Zend_Db_Table_Row_Abstract) {
            $this->_helper->viewRenderer->render('near.error');
            return;
        }

        $select = $model->select()
        ->limit(10);

        try {
            $this->view->rowset = $row->findNearRowset($select)->toArray();
        } catch (Zend_Db_Table_Exception $e) {
            // logowanie wyjatku
            Zend_Registry::get('logger')
            ->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::ERR);
        }
    }
     */

    /**
     * Pokaz prezentacje rekordu
     * @todo dodać cache!
     */
    public function showAction() {
        $primaryId = $this->_getParam('id');

        $f = new Zend_Filter_Int();
        $primaryId = $f->filter($primaryId);

        if (null === $primaryId) {
            $this->_helper->viewRenderer->render('show.error');
            return;
        }

        if ($this->_hasParam('_site')) {
            $this->_helper->system->layout('catalog_site');
        }

        $model = new Catalog_Model_Catalog();
        // TODO: Cache!
        $catalogRow = $model->findByIdCache($primaryId, false);

        if (!$catalogRow instanceof Zend_Db_Table_Row_Abstract) {
            $this->_helper->viewRenderer->render('show.error');
            return;
        }

        $this->view->catalogRow = $catalogRow;
        // ustawiam parametr dla helpera
        $this->view->categoryTree()->setActiveId($catalogRow->catalog_district_id);

//         /* @var $flashMessanger Zend_Controller_Action_Helper_FlashMessenger */
//        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        $tab = strtolower($this->_getParam('tab','ogolne','personel'));
        $this->view->tab = $tab;
        switch ($tab) {
            default:
			case 'ogolne':
				$this->view->timeRow = $model->findAllTimeCache($catalogRow);
//				$flashMessenger->addMessage($model->getStatus());
				$this->view->optionsRowset = $model->findAllOptionsCache($catalogRow);
//				$flashMessenger->addMessage($model->getStatus());
				break;
			case 'uslugi':
				$this->view->serviceRowset = $model->findAllServiceCache($catalogRow);
//				$flashMessenger->addMessage($model->getStatus());
				break;
			case 'personel':
				$this->view->staffRowset = $model->findAllStaffCache($catalogRow);
//				$flashMessenger->addMessage($model->getStatus());
				break;
        }

        $this->_setupModelCatalogImage();
        $this->view->imagesRowset = $model->findAllImageCache($catalogRow);

//        $flashMessenger->addMessage($model->getStatus());
        
       
//        array_map(array($flashMessenger,'addMessage'), $model->getMessages());
    }

    

    /**
     * Wyszukiwarka
     * @todo dodac cache! - np. catalog_district i sprawdzanie czy istnieje .. czyszczone gdy zostanie dodana nowa dzielnica
     * @todo optymalizować
     */
    public function searchAction() {
        $config = $this->_helper->config('index.xml');

        $rq = $this->getRequest();
        $data = $rq->getParams();

        $f = new KontorX_Filter_MagicQuotes();
        $data = $f->filter($data);
        $this->view->input = $data;

        $district = new CatalogDistrict();
        $this->view->districtRowset = $district->fetchAllCache();

        $options = new CatalogOptions();
        $this->view->optionsArray = $options->fetchAllOptionsArrayCache();

        $service = new CatalogService();
        $this->view->serviceArray = $service->fetchAllOptionsArrayCache();

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

    

//    private function _initAlphabetical(Zend_Db_Select $select) {
//        $this->view->az = $az = array(
//                'A','B','C','Ć','D','E','F','G','H',
//                'I','J','K','L','Ł','M','N','Ń','O',
//                'P','R','S','Ś','T','U','W','Y','Z',
//                'Ź','Ż');
//
//        $string = $this->_getParam('string');
//        $string = strtoupper($string);
//
//        $this->view->string = $string;
//
//        if (in_array($string, $az)) {
//            // setup select
//            $select
//            ->where('name LIKE ?', "$string%")
//            ->order('name ASC');
//        }
//    }

    private function _setupModelCatalogType() {
        $config = $this->_helper->config();
        $path = $config->path->upload->type;
        $path = $this->_helper->system()->getPublicHtmlPath($path);

        require_once 'catalog/models/CatalogType.php';
        CatalogType_Row::setUploadPath($path);
    }

    private function _setupModelCatalogImage() {
        $config = $this->_helper->config();
        $path = $config->path->upload->image;
        $path = $this->_helper->system()->getPublicHtmlPath($path);

        require_once 'catalog/models/CatalogImage.php';
        CatalogImage_Row::setUploadPath($path);
    }

    private function _setupModelCatalogService() {
        $config = $this->_helper->config();
        $path = $config->path->upload->service;
        $path = $this->_helper->system()->getPublicHtmlPath($path);

        require_once 'catalog/models/CatalogService.php';
        CatalogService_Row::setUploadPath($path);
    }
}