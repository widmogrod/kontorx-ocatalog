<?php
class Catalog_Time2Controller extends Promotor_Controller_Action_Scaffold {
	
	public $skin = array(
		// layout
		'layout' => 'admin_catalog',
		// rozszerzam konfiguracje
		'config' => array(
			'filename' => 'backend_config.ini'
		),
	);
	
//	public $acl = array(
//		'list' => 'list',
//		'add' => 'create',
//		'edit' => 'update',
//		'display' => 'show',
//		'delete' => 'delete'
//	);

//	public $contexts = array(
//		'display' => array('xml')
//	);

	/**
	 * @see Promotor_Controller_Action_Scaffold specialization
	 */
	protected $_modelClass = 'Catalog_Model_CatalogTime2';
	protected $_formAddClass = 'Catalog_Form_CatalogTimeAdd';
	protected $_formEditClass = 'Catalog_Form_CatalogTimeEdit';
	protected $_formRemoveClass = 'Catalog_Form_CatalogTimeRemove';
	
	protected $_addPostObservableName = 'catalog_time_add_post';
	protected $_editPostObservableName = 'catalog_time_edit_post';
	protected $_deletePostObservableName = 'catalog_time_delete_post';
	
	public function init() {
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		if (!$contextSwitch->hasContext('autoviewer')) {
			$contextSwitch->addContext('autoviewer', array(
				'suffix' => 'autoviewer',
				'callbacks' => array(
					'TRIGGER_INIT' => array($this,'_postCallback')
				)
			));
		}
        $contextSwitch->initContext();
	}
	
	public function indexAction() {
		$this->_forward('list');
	}
	
	public function listAction() {
		$model = new Catalog_Model_CatalogTime2();
		$table = $model->getDbTable();

		$rq = $this->getRequest();
		if ($rq->isPost()) {
			switch ($rq->getPost('action_type')) {
				case 'update':
					if (null !== $rq->getPost('editable')) {
//						if ($this->_helper->acl->isAllowed('update')) {
							$data = $rq->getPost('editable');
							$model->editableUpdate($data);
							$this->_helper->flashMessenger($model->getStatus());
							$this->_helper->redirector->goToAndExit(array());
//						}
					}
					break;
				case 'delete':
					if (null !== $rq->getPost('action_checked')) {
//						if ($this->_helper->acl->isAllowed('delete')) {
							$data = $rq->getPost('action_checked');
							$model->editableDelete($data);
							$this->_helper->flashMessenger($model->getStatus());
							$this->_helper->redirector->goToAndExit(array());
//						}
					}
					break;
			}			
		}

		$select = $model->selectList();

		// setup data grid
		$config = $this->_helper->config('time2.xml');
		$grid = KontorX_DataGrid::factory($select, $config->grid);
		$grid->setValues($this->_getAllParams());
		
		$this->_setupDataGridPaginator($grid);

		$this->view->grid = $grid;
	}
	
	/**
	 * @return void
	 */
	public function addweekAction() {
		$model = new Catalog_Model_Catalog();
		// nie używam cachowania..
		$this->view->catalogList = $model->fetchAllAsPair();

		$rq = $this->getRequest();

		if (!$rq->isPost()) {
			return;
		}

		$catalogId = $this->_getParam('catalogId');
		$data = $rq->getPost('week');

		$model = new Catalog_Model_CatalogTime2();
		$model->setWeekTime($catalogId, $data);
		
		$status = $model->getStatus();
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage($status);
		array_map(array($flashMessenger,'addMessage'), $model->getMessages(true));

		if ($status !== Catalog_Model_CatalogTime2::SUCCESS) {
			$this->_helper->redirector->goTo('addweek');
		} else {
			$this->_helper->redirector->goTo('addweek');
		}
	}

	/**
	 * Konvertuje z starej wersji stomatologów
	 * @return void
	 */
	public function convertAction() {
		// run convert
		$model = new Catalog_Model_CatalogTime2();
		$model->convert();

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		// add messages
		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger,'addMessage'), $model->getMessages());

		$this->_helper->redirector->goTo('list');
	}
}