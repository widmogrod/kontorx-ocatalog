<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_ServicecostController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	protected $_modelClass = 'CatalogServiceCost';

	public function listAction() {
		$this->view->addHelperPath('KontorX/View/Helper');
		
		$config = $this->_helper->config('servicecost.ini');
		
		$model = new Catalog_Model_HasService();

		$grid = KontorX_DataGrid::factory($model->selectWithCatalog());
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
		$form = new Catalog_Form_ServiceAddMulti();
		$model = new Catalog_Model_HasService();
		
		$catalogId = $this->_getParam('catalog_id');

		if (!$rq->isPost()) {
			$form
				->setDefault('catalog_id', $catalogId)
				->setDefault('service', $model->fetchAllAsPair($catalogId));

			$this->view->form = $form;
			return;
		}

		if (!$form->isValid($rq->getPost())) {
			$this->view->form = $form;
			return;
		}

		$catalogId = $form->getValue('catalog_id');
		$data = $form->getValue('service');
		$desc = $rq->getPost('desc');

		$model->saveService($catalogId, $data, $desc);
		
		$status = $model->getStatus();
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage($status);
		array_map(array($flashMessenger,'addMessage'), $model->getMessages(true));

		if ($status !== Catalog_Model_HasService::SUCCESS) {
			$this->view->form = $form;
		} else {
			$this->_helper->redirector->goTo('addmulti');
		}
	}
}