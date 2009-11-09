<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Language_AdminController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	protected $_modelClass = 'Language';
	
	public function indexAction() {
		$this->_forward('list');
	}

	/**
	 * @Overwrite
	 */
    protected function _listFetchAll() {
    	$page = $this->_getParam('page',1);
    	$rowCount = $this->_getParam('rowCount',30);

    	$model = $this->_getModel();
    	$db = $model->getAdapter();
    	
    	// select dla danych
		$select = $model->select();
		$select
			->limitPage($page, $rowCount);
    	$rowset = $model->fetchAll($select);

    	// select dla paginacji
    	require_once 'Zend/Db/Select.php';
    	$select = new Zend_Db_Select($db);
    	$select
    		->from(array('table' => $model->info(Zend_Db_Table::NAME)));

		// paginacja
		require_once 'Zend/Paginator.php';
    	$paginator = Zend_Paginator::factory($select);
    	$paginator->setCurrentPageNumber($page);
    	$paginator->setItemCountPerPage($rowCount);
    	$this->view->paginator = $paginator;
    	
    	return $rowset;
    }
}

