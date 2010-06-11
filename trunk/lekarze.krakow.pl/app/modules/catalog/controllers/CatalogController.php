<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_CatalogController extends KontorX_Controller_Action_CRUD {

	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	public $contexts = array(
		'list' => array('body'),
		'add' => array('body'),
		'edit' => array('body')
	);
	
	protected $_modelClass = 'Catalog';
	
	protected $_addPostObservableName = 'catalog_default_add_post';
	protected $_editPostObservableName = 'catalog_default_edit_post';
	protected $_deletePostObservableName = 'catalog_default_delete_post';

	protected function _getConfigFormFilename($controller) {
		return strtolower("$controller.xml");
	}

	public function init() {
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		// wylanczam wylaczenie layotu
		$contextSwitch->setAutoDisableLayout(false);
		if (!$contextSwitch->hasContext('body')){
			// nowy context
			$contextSwitch->addContext('body',array(
				'callbacks' => array(
					'init' => array($this, 'contextSwitchBodyCallback')
				)
			));
		}
		$contextSwitch->initContext();

		$this->view->apiKey = G_MAP_KEY;

		parent::init();
	}

	/**
	 * Callback of @see init and @see ContextSwitch
	 * @return void
	 */
	public function contextSwitchBodyCallback() {
		// zmieniam szablon
		$this->_helper->system->layout('admin_body');
	}

	public function listAction() {
		$model = new Catalog_Model_Catalog();
		$table = $model->getDbTable();

		$rq = $this->getRequest();
		if ($rq->isPost()) {
			switch ($rq->getPost('action_type')) {
				case 'update':
					if (null !== $rq->getPost('editable')) {
						$data = $rq->getPost('editable');
						$model->editableUpdate($data);
						$this->_helper->flashMessenger($model->getStatus());
						$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
					}
					break;
				case 'delete':
					if (null !== $rq->getPost('action_checked')) {
						$data = $rq->getPost('action_checked');
						$model->editableDelete($data);
						$this->_helper->flashMessenger($model->getStatus());
						$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
					}
					break;
			}			
		}

		$config = $this->_helper->config('catalog.xml');
		
		$grid = KontorX_DataGrid::factory($table, $config->grid);
		$grid->setValues($this->_getAllParams());

		// setup grid paginatior
		$select = $grid->getAdapter()->getSelect();
		
		$paginator = Zend_Paginator::factory($select);
		$grid->setPaginator($paginator);
		$grid->setPagination($this->_getParam('page'), 20);

		$this->view->grid = $grid;
		$this->view->actionUrl = $this->_helper->url('list');
	}

	/**
     * @Overwrite
     */
    protected function _addPrepareData(Zend_Form $form) 
    {
    	$data = parent::_addPrepareData($form);
    	
    	require_once 'user/models/User.php';
    	$userId = User::getAuth(User::AUTH_USERNAME_ID);

    	$data['user_id'] 	  = $userId;
    	return $data;
    }
	
	/*public function init() {
		$this->_helper->acl->setAccess(true);
	}*/
    
    public function clearcacheAction() {
    	$this->_helper->getHelper('viewRenderer')->setNoRender();
    	$this->_helper->getHelper('layout')->disablelayout();

		$manager = Promotor_Observable_Manager::getInstance();
		$list = $manager->notify(
			'catalog_clear_all'
		);

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		foreach ($list->getMessages() as $observerName => $messages) {
			$message = sprintf('%s=%s', implode('<br />', $messages), $observerName);
			$flashMessenger->addMessage($message);
		}
		
		$this->_helper->getHelper('redirector')->goto('list');
    }
}