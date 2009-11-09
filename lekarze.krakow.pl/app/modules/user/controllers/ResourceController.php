<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class User_ResourceController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin_user',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	protected $_modelClass = 'RoleResource';

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
			->order('name ASC')
			->limitPage($page, $rowCount);

    	$rowset = $model->fetchAll($select);

    	// select dla paginacji
		$this->_preparePagination($select);

    	return $rowset;
    }

    /**
     * @Overwrite
     * @return Zend_Form
     */
    protected function _addGetForm() {
    	$form = parent::_addGetForm();

    	$model = $this->_getModel();
    	$where = 'name = ?';

    	// setup @see Zend_Form
    	$this->_setupZendForm($form, $model, $where);
    	return $form;
    }

	/**
     * @Overwrite
     * @return Zend_Form
     */
    protected function _editGetForm(Zend_Db_Table_Row_Abstract $row) {
    	$form = parent::_editGetForm($row);

    	$model = $this->_getModel();
    	$where = 'name = ? AND id <> :id';

    	// setup @see Zend_Form
    	$this->_setupZendForm($form, $model, $where);
    	return $form;
    }

    /**
     * Ustawia opcje formularza
     *
     * @param Zend_Folm $form
     * @param Zend_Db_Table_Abstract $model
     * @param Zend_Db_Select $select
     */
    protected function _setupZendForm(Zend_Form $form, Zend_Db_Table_Abstract $model, $where) {
    	require_once 'KontorX/Validate/DbTable.php';
    	$nameValid = new KontorX_Validate_DbTable($model, $where, true, KontorX_Validate_DbTable::REQUEST);
//    	$nameValid->setWhere($where);
//    	$nameValid->setUniqValue(true);

    	$form
    		->getElement('name')
    		->addValidator($nameValid);
    }
}