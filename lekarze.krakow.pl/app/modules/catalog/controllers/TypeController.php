<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_TypeController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini'),
		'type' => array(
			'layout' => 'catalog_type'
		)
	);

	protected $_modelClass = 'CatalogType';

	protected function _getModel() {
		if (null == $this->_model) {
			$this->_model = parent::_getModel();

			$config = $this->_helper->config();
			$path = $config->path->upload->type;
			$path = $this->_helper->system()->getPublicHtmlPath($path);
			
			CatalogType_Row::setUploadPath($path);
		}
		return $this->_model;
	}
	
	public function listAction() {
		$this->view->addHelperPath('KontorX/View/Helper');
		
		$config = $this->_helper->config('type.ini');
		
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
	 * Wyświetlanie listy wizytówek oferujących usługę
	 * 
	 * @return void
	 */
	public function typeAction() {
		$config = $this->_helper->config('list.ini');

		$page = $this->_getParam('page', 1);
		$rowCount = $config->rowCount;

		$model = new Catalog_Model_Type();

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

	/**
	 * Overwrite
	 */
	protected function _addGetForm() {
		$form = parent::_addGetForm();

		// setup @see Zend_Form
		$this->_setupZendForm($form);

		return $form;
	}

	protected function _addInsert(Zend_Form $form) {
		$data = $this->_addPrepareData($form);

		try {
			// dodawanie rekordu
	    	$model = $this->_getModel();
	    	$row = $model->createRow($data);
	    	$row->save();
		} catch (KontorX_Db_Table_Row_FileUpload_Exception $e) {
			if($row->hasMessages()) {
				$flashMessenger = $this->_helper->flashMessenger;
				foreach ($row->getMessages() as $message) {
					$flashMessenger->addMessage($message);
				}
			}
			
			throw new KontorX_Db_Table_Row_FileUpload_Exception(implode("\n",$row->getMessages()));
		}

		return $row;
	}
	
	/**
	 * Overwrite
	 */
	protected function _editGetForm(Zend_Db_Table_Row_Abstract $row) {
		$form = parent::_editGetForm($row);

		// setup @see Zend_Form
		$this->_setupZendForm($form, true);

		return $form;
	}
	
	/**
     * Ustawia opcje formularza
     *
     * @param Zend_Folm $form
     * @param bool $edit
     */
    protected function _setupZendForm(Zend_Form $form, $edit = false) {
//    	$form->setAttrib('enctype','multipart/form-data');

//    	require_once 'KontorX/Form/Element/File.php';
//		$form->addElement(new KontorX_Form_Element_File('ico'), 'ico');
//		$image = $form->getElement('ico');
//		$image->setLabel('ico');

//		if (true === $edit) {
//			$image->setIgnore(true);
//		}
    }
}