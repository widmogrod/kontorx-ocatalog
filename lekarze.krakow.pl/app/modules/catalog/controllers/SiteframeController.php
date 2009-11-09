<?php
class Catalog_SiteframeController extends Promotor_Controller_Action_Scaffold {

	public $skin = array(
//		// forsuje domyslny template i style na admin
//		'template' => 'admin',
//		'style' => 'default',
		// layout
		'layout' => 'admin_catalog',
		// rozszerzam konfiguracje
		'config' => array(
			'filename' => 'backend_config.ini'
		),

		// specjalizuje template dla akcji
		'display' => array(
			'layout' => 'catalog_show_frame'
		),
		// specjalizuje template dla akcji
		'gohttp' => array(
			'layout' => 'catalog_show_frame'
		)
	);

	public $ajaxable = array(
		'display' => array('html','html#main'),
		'list' => array('jsTree'),
	);
	
	/*public $acl = array(
		'list' => 'list',
		'export' => 'list',
		'add' => 'create',
		'addfromodt' => 'create',
		'edit' => 'update',
		'move' => 'update',
		'display' => 'show',
		'delete' => 'delete'
	);*/
	
	/**
	 * @see Promotor_Controller_Action_Scaffold specialization
	 */
	protected $_modelClass = 'Catalog_Model_SiteFrame';
	protected $_formAddClass = 'Catalog_Form_SiteFrameAdd';
	protected $_formEditClass = 'Catalog_Form_SiteFrameEdit';
	protected $_formRemoveClass = 'Catalog_Form_SiteFrameRemove';
	
	protected $_addPostObservableName = 'catalog_site_frame_add_post';
	protected $_editPostObservableName = 'catalog_site_frame_edit_post';
	protected $_deletePostObservableName = 'catalog_site_frame_delete_post';
	
	public function indexAction() {
		$this->_forward('list');
	}
	
	public function listAction() {
		$model = new Catalog_Model_SiteFrame();
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
//						}
					}
					break;
				case 'delete':
					if (null !== $rq->getPost('action_checked')) {
//						if ($this->_helper->acl->isAllowed('delete')) {
							$data = $rq->getPost('action_checked');
							$model->editableDelete($data);
							$this->_helper->flashMessenger($model->getStatus());
//						}
					}
					break;
			}			
		}

		// setup data grid
		$config = $this->_helper->config('siteframe.xml');
		$grid = KontorX_DataGrid::factory($table, $config->grid);
		$grid->setValues($this->_getAllParams());

		$this->view->grid = $grid;
	}
	
	/**
     * Pokaż stronę jako ramkę
     * @return void
     */
    public function displayAction() {
		$model = new Catalog_Model_SiteFrame();
		$data = $model->findByAliasCache($this->_getParam('alias'));

		$this->view->data = $data;
		
		if (isset($data['uri'])) {
			$this->view->uri = $data['uri'];
		} else {
			$this->_forward('index','index','catalog');
		}
    }
    
	/**
     * Pokaż stronę jako ramkę
     * @return void
     */
    public function gohttpAction() {
    	$catalogId = $this->_getParam('id');

		$model = new Catalog_Model_Catalog();
		$data = $model->findByIdCache($catalogId);

    	if (isset($data['www'])) {
			$this->view->uri = $data['www'];
			$this->view->data = $data;
		} else {
			$this->_forward('show','index','catalog', array('id' => $catalogId));
			return;
		}
    }
}