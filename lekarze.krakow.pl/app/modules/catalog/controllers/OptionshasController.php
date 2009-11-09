<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_OptionshasController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini'
		)
	);

	public $contexts = array(
		'list' => array('body'),
		'add' => array('body'),
		'edit' => array('body')
	);
	
	protected $_modelClass = 'CatalogHasCatalogOptions';
	
	protected function _getConfigFormFilename($controller) {
		return strtolower("$controller.xml");
	}

	public function init() {
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		// wylanczam wylaczenie layotu
		$contextSwitch->setAutoDisableLayout(false);
		if (!$contextSwitch->hasContext('body')) {
			// nowy context
			$contextSwitch->addContext('body',array(
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
		
		$config = $this->_helper->config('optionshas.xml');
		
		$model = $this->_getModel();
		
		require_once 'Zend/Db/Select.php';
		$select = new Zend_Db_Select($model->getAdapter());
		
		$select
			->from(array('chco' => 'catalog_has_catalog_options'))
			->joinLeft(array('c' => 'catalog'), 'c.id = chco.catalog_id', array('name' => 'c.name'))
			->joinLeft(array('co' => 'catalog_options'), 'co.id = chco.catalog_options_id', array('option' => 'co.name'));

		$grid = KontorX_DataGrid::factory($select);
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
	 * @return void
	 */
	public function addmultiAction() {
		$rq = $this->getRequest();
		$form = new Catalog_Form_OptionsAddMulti();
		$model = new Catalog_Model_HasOptions();
		
		$catalogId = $this->_getParam('catalog_id');

		if (!$rq->isPost()) {
			$form
				->setDefault('catalog_id', $catalogId)
				->setDefault('options', $model->fetchAllAsPair($catalogId));

			$this->view->form = $form;
			return;
		}

		if (!$form->isValid($rq->getPost())) {
			$this->view->form = $form;
			return;
		}

		$catalogId = $form->getValue('catalog_id');
		$data = $form->getValue('options');

		$model->saveOptions($catalogId, $data);

		$status = $model->getStatus();
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage($status);
		array_map(array($flashMessenger,'addMessage'), $model->getMessages(true));

		if ($status !== Catalog_Model_HasOptions::SUCCESS) {
			$this->view->form = $form;
		} else {
			$this->_helper->redirector->goTo('addmulti');
		}
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
}