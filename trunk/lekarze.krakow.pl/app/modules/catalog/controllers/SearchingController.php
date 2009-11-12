<?php
class Catalog_SearchingController extends Promotor_Controller_Action_Scaffold {
	
	public $skin = array(
		// layout
		'layout' => 'admin_catalog',
		// rozszerzam konfiguracje
		'config' => array(
			'filename' => 'backend_config.ini'
		),
	);
	
	/**
	 * @see Promotor_Controller_Action_Scaffold specialization
	 */
	protected $_modelClass = 'Catalog_Model_Searching';
	protected $_formAddClass = 'Catalog_Form_SearchingAdd';
	protected $_formEditClass = 'Catalog_Form_SearchingEdit';
	protected $_formRemoveClass = 'Catalog_Form_SearchingRemove';
	
//	protected $_addPostObservableName = 'catalog_search_query_add_post';
//	protected $_editPostObservableName = 'catalog_search_query_edit_post';
//	protected $_deletePostObservableName = 'catalog_search_query_delete_post';
	
	public function indexAction() {
		$this->_forward('list');
	}
	
	public function listAction() {
		$model = new Catalog_Model_Searching();
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
		$config = $this->_helper->config('searching.xml');
		$grid = KontorX_DataGrid::factory($select, $config->grid);
		$grid->setValues($this->_getAllParams());
		
		$this->_setupDataGridPaginator($grid);

		$this->view->grid = $grid;
	}
}