<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_OptionsController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini'
		),
		'options' => array(
			'layout' => 'catalog_options'
		)
	);

	public $contexts = array(
		'list' => array('body'),
		'add' => array('body'),
		'edit' => array('body')
	);
	
	protected $_modelClass = 'CatalogOptions';
	
	protected function _getConfigFormFilename($controller) {
		return strtolower("$controller.xml");
	}

	public function init() {
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		if (!$contextSwitch->hasContext('body')) {
			$contextSwitch
				// wylanczam wylaczenie layotu
				->setAutoDisableLayout(false)
				// nowy context
				->addContext('body',array(
					'callbacks' => array(
						'init' => array($this, 'contextSwitchBodyCallback')
					)
				));
		}
		$contextSwitch->initContext();

		parent::init();
	}
	
	public function listAction() {
		$this->view->addHelperPath('KontorX/View/Helper');
		
		$config = $this->_helper->config('options.xml');
		
		$model = $this->_getModel();

		$grid = KontorX_DataGrid::factory($model);
		$grid->setColumns($config->dataGridColumns->toArray());
		$grid->setValues((array) $this->_getParam('filter'));
		
		// setup grid paginatior
		$select = $grid->getAdapter()->getSelect();
		$paginator = Zend_Paginator::factory($select);
		$grid->setPaginator($paginator);
		$grid->setPagination($this->_getParam('page'), 20);

		$this->view->grid = $grid;
		$this->view->actionUrl = $this->_helper->url('list');
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
	 * Wyświetlanie listy wizytówek oferujących usługę
	 * 
	 * @return void
	 */
	public function optionsAction() {
		$config = $this->_helper->config('list.ini');

		$page = $this->_getParam('page', 1);
		$rowCount = $config->rowCount;

		$model = new Catalog_Model_Options();

		if ($this->_hasParam('id')) {
			$data = $model->findAllAsCatalogListCache($this->_getParam('id'), $page, $rowCount);
		} else
		if ($this->_hasParam('alias')) {
			$data = $model->findAllAsCatalogListCache($this->_getParam('alias'), $page, $rowCount);
		} else {
			$this->_forward('index','list','catalog');
			return;
		}

		list ($rowset, $select, $row) = $data;
		$this->view->row = $row;
		$this->view->rowset = $rowset;

		if ($select instanceof Zend_Db_Select) {
			$paginator = Zend_Paginator::factory($select);
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage($rowCount);
			$this->view->paginator = $paginator;
		}
	}
}