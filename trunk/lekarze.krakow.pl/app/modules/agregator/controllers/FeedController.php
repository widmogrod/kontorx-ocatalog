<?php
class Agregator_FeedController extends Zend_Controller_Action
{
	public $skin = array(
		'layout' => 'admin_feed',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	public function listAction()
	{
		$model = new Agregator_Model_Feed();
		
		$select = $model->selectList();
		$config = $this->getHelper('Config')->config('feed.xml');
		
		$grid = KontorX_DataGrid::factory($select, $config->grid);
		$grid->setValues($this->_getAllParams());
//		$grid->getRenderer();
		$this->view->grid = $grid;
	}

	/**
	 * Zbieranie wszystkich wpisów z feeda
	 * 
	 * @return void
	 */
	public function agregateAction()
	{
		$rq = $this->getRequest();
		
		$model = new Agregator_Model_Agregator();
		$model->agregate($rq->getParam('id'));
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->getHelper('FlashMessenger');
		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger, 'addMessage'), $model->getMessages(true));

		$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
	}
	
	/**
	 * Dodaj nowy feed
	 * 
	 * @return void|void
	 */
	public function addAction()
	{
		$rq = $this->getRequest();
		
		$form = new Agregator_Form_Feed_Add();
		$this->view->form = $form;

		if (!$rq->isPost())
		{
			return;
		}
		
		if (!$form->isValid($rq->getPost()))
		{
			return;
		}

		$model = new Agregator_Model_Feed();
		$model->add($form->getValue('url'));
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->getHelper('FlashMessenger');
		array_map(array($flashMessenger, 'addMessage'), $model->getMessages(true));

		if ($model->getStatus() === Agregator_Model_Feed::SUCCESS)
		{
			$this->getHelper('Redirector')->goToUrl(getenv('HTTP_REFERER'));
		}
	}
}