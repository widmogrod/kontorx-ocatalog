<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_SiteController extends KontorX_Controller_Action_CRUD {

    public $skin = array (
        'layout' => 'admin_catalog',
        'config' => array(
            'filename' => 'backend_config.ini'),

        'show' => array(
            'layout' => 'catalog_show',
            'lock' => true
        ),
        'www' => array(
            'layout' => 'catalog_show',
            'lock' => true
        )
    );

    public $contexts = array(
        'list' => array('body'),
        'add' => array('body'),
        'edit' => array('body')
    );

    protected $_modelClass = 'CatalogSite';

    protected $_configFilenameExtension = "xml";

    public function init() {
        $this->view->addHelperPath('KontorX/View/Helper');

        /* @var $contextSwitch Zend_Controller_Action_Helper_ContextSwitch */
        $contextSwitch = $this->_helper->getHelper('ContextSwitch');

        // wylanczam wylaczenie layotu
        $contextSwitch->setAutoDisableLayout(false);
        if (!$contextSwitch->hasContext('body', false)) {
        	try {
	            // nowy context
	            $contextSwitch->addContext('body',array('callbacks' => array(
	                'init' => array($this, 'contextSwitchBodyCallback'))));
        	} catch (Exception $e) {}
        }
        $contextSwitch->initContext();

        parent::init();
    }

    /**
     * Callback of @see init and @see ContextSwitch
     * @return void
     */
    public function contextSwitchBodyCallback() {
        // zmieniam szablon
        $system = $this->_helper->system;
        $system->layout('admin_body');
        $system->setLayoutSectionName('admin_catalog');
        $system->lockLayoutName(true);
    }

    /**
     * @return void
     */
    public function indexAction() {
        $this->_forward('list');
    }

    /**
     * @return void
     */
    public function listAction() {
        $this->view->addHelperPath('KontorX/View/Helper');

        $config = $this->_helper->config('site.xml');

        $model = $this->_getModel();
        $select = new Zend_Db_Select($model->getAdapter());
        $select
        ->from(array('cs' => 'catalog_site'),Zend_Db_Select::SQL_WILDCARD)
        ->joinLeft(array('c' => 'catalog'),
            'cs.catalog_id = c.id',
            array('catalog_name'=>'c.name'));

        $grid = KontorX_DataGrid::factory($select, $config->dataGrid);

        $paginator = Zend_Paginator::factory($select);
        $grid->setPaginator($paginator);
        $grid->setPagination($this->_getParam('page'), 20);

        $this->view->grid = $grid;
        $this->view->actionUrl = $this->_helper->url('list');
    }

	/**
     * Pokaż wizytowke - jako strone
     * @return void
     */
    public function wwwAction() {
    	$alias = strtolower($this->_getParam('alias'));

        // prefix www.weterynarze.krakow.pl
        if ($alias === 'www') {
			$this->_forward('index','index','catalog');
			return;
        }

        // szukaj strony wizytówki
        $model = new Catalog_Model_Catalog();
//        $model->findByAlias();
        $row = $model->findByAliasCache($alias, false);

        $model->_log('catalog:show:www', Zend_Log::DEBUG);
        
        if ($row instanceof Zend_Db_Table_Row_Abstract) {
        	$model->_log('find CATALOG by alias: ', $row->alias, Zend_Log::DEBUG);

        	// strona wizytówki
        	$this->_forward('show','index','catalog',array('id' => $row->id,'_site' => 1));
        	return;
        } else {
   			// szukaj typu
			$model = new Catalog_Model_Type();
			$row = $model->findByAliasCache($alias, false);

			if ($row instanceof Zend_Db_Table_Row_Abstract) {
				$model->_log('find TYPE by alias: ', $row->alias, Zend_Log::DEBUG);

				// strona typu
        		$this->_forward('type','type','catalog',array('id' => $row->id));
        		return;
			} else {
				// szukaj usługi
				$model = new Catalog_Model_Service();
				$row = $model->findByAliasCache($alias, false);
	
				if ($row instanceof Zend_Db_Table_Row_Abstract) {
					$model->_log('find SERVICE by alias: ', $row->alias, Zend_Log::DEBUG);

					// strona usługi
	        		$this->_forward('service','service','catalog',array('id' => $row->id));
	        		return;
				} else {
					// szukaj gabinet oferuje
					$model = new Catalog_Model_Options();
					$row = $model->findByAliasCache($alias, false);

					if ($row instanceof Zend_Db_Table_Row_Abstract) {
						$model->_log('find OPTIONS by alias: ', $row->alias, Zend_Log::DEBUG);

						// strona gabinet orefuje
	        			$this->_forward('options','options','catalog',array('id' => $row->id));
	        			return;
					}
				}
			}
        }

        $model->_log('NO find! by alias: ' . $alias, Zend_Log::DEBUG);
        $model->_log(sprintf('HTTP Referer: %s', getenv('HTTP_REFERER')), Zend_Log::DEBUG);
        
        $model->_log('Messages: ', Zend_Log::DEBUG);
        foreach($model->getMessages(true) as $message) {
        	$model->_log($message, Zend_Log::DEBUG);
        }
        
        $this->_helper->redirector->gotoUrl(
        	$this->_helper->url->url(array(),'catalog-main')
        );
    }
}