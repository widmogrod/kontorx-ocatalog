<?php
class System_UpdateController extends Promotor_Controller_Action {

	public $skin = array(
		// forsuje domyslny template i style na admin
		'template' => 'admin',
		'style' => 'default',
		// layout
		'layout' => 'admin_system',
		// rozszerzam konfiguracje
		'config' => array(
			'filename' => 'backend_config.ini'
		)
	);
	
	public $acl = array(
		'index' => 'list',
		'update' => 'create',
		'downgrade' => 'create'
	);

	/**
	 * @return void
	 */
	public function indexAction() {
		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();

		$config = $this->_helper->config('update.xml');

		$model = new System_Model_Update(APP_MODULES_PATHNAME);
		$rowset = $model->findAll();

		// setup data grid
		$grid = KontorX_DataGrid::factory($rowset, $config->grid);
		$grid->setValues($rq->getQuery());

		$this->view->grid = $grid;
	}

	/**
	 * @return void
	 */
	public function updateAction() {
		$this->_helper->viewRenderer->setNoRender();

		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$model = new System_Model_Update(APP_MODULES_PATHNAME);

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		if (null !== ($name = $rq->getParam('id'))) {
			$force = (bool) $rq->getParam('force', false);
			$revision = $rq->getParam('revision');
	
			$model->update($name, $force);
	
			$flashMessenger->addMessage($model->getStatus());
			array_map(array($flashMessenger,'addMessage'), $model->getMessages(true));
		} else {
			$flashMessenger->addMessage('NO_VALID');
		}

		$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
	}
	
	/**
	 * @return void
	 */
	public function downgradeAction() {
		$this->_helper->viewRenderer->setNoRender();

		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$model = new System_Model_Update(APP_MODULES_PATHNAME);

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		if (null !== ($name = $rq->getParam('id'))) {
			$force = (bool) $rq->getParam('force', false);
			$revision = $rq->getParam('revision');
	
			$model->downgrade($name, $force);
	
			$flashMessenger->addMessage($model->getStatus());
			array_map(array($flashMessenger,'addMessage'), $model->getMessages(true));
		} else {
			$flashMessenger->addMessage('NO_VALID');
		}

		$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
	}
}