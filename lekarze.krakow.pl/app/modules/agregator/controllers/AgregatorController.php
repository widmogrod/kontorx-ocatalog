<?php
class Agregator_AgregatorController extends Promotor_Controller_Action_Scaffold
{
	public $skin = array(
		'layout' => 'admin_feed',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	public function listAction()
	{
		$model = new Agregator_Model_Agregator();
		
		$select = $model->selectList();
		$config = $this->getHelper('Config')->config('agregator.xml');
		
		$grid = KontorX_DataGrid::factory($select, $config->grid);
		$grid->setValues($this->_getAllParams());
		$this->view->grid = $grid;
	}
	
	
	/**
	 * Zbieranie wszystkich wpisów z wszystkich feedów
	 * 
	 * @return void
	 */
	public function agregateallAction()
	{
		$rq = $this->getRequest();
		
		$model = new Agregator_Model_Agregator();
		$model->multiAgregate();
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->getHelper('FlashMessenger');
		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger, 'addMessage'), $model->getMessages(true));

		$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
	}
	
	/**
	 * Dodaj nowy wpis
	 * 
	 * @return void|void
	 */
	public function addAction()
	{
		$rq = $this->getRequest();
		
		$form = new Agregator_Form_Agregator_Add();
		$this->view->form = $form;

		if (!$rq->isPost())
		{
			return;
		}
		
		if (!$form->isValid($rq->getPost()))
		{
			return;
		}

		$model = new Agregator_Model_Agregator();
		$model->add($form->getValues());
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->getHelper('FlashMessenger');
		array_map(array($flashMessenger, 'addMessage'), $model->getMessages(true));

		if ($model->getStatus() === Agregator_Model_Feed::SUCCESS)
		{
			$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
		}
	}
	
	/**
	 * Edytuj wpis
	 * 
	 * @return void|void
	 */
	public function editAction()
	{
		$rq = $this->getRequest();
		$id = $rq->getParam('id');
		
		$model = new Agregator_Model_Agregator();
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->getHelper('FlashMessenger');
		
		$row = $model->findByPK($id);
		if (!($row instanceof Zend_Db_Table_Row_Abstract))
		{
			$flashMessenger->addMessage('Rekord o ID "'.$id.'" nie istnieje!');
			$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
			return;
		}
		
		$form = new Agregator_Form_Agregator_Edit(array(
			'primaryKey' => $id
		));
		$this->view->form = $form;

		if (!$rq->isPost())
		{
			$form->setDefaults($row->toArray());
			return;
		}
		
		if (!$form->isValid($rq->getPost()))
		{
			return;
		}

		$model->edit($id, $form->getValues());
		
		array_map(array($flashMessenger, 'addMessage'), $model->getMessages(true));

		if ($model->getStatus() === Agregator_Model_Feed::SUCCESS)
		{
			$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
		}
	}
	
	/**
	 * Usunięcie określonego wpisu
	 * @return void
	 */
	public function deleteAction()
	{
		$model = new Agregator_Model_Agregator();
		$model->delete($this->_getParam('id'));
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->getHelper('FlashMessenger');
		array_map(array($flashMessenger, 'addMessage'), $model->getMessages(true));

		$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
	}
}