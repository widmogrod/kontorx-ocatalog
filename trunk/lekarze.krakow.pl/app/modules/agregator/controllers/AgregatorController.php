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
	 * Usunięcie określonego wpisu RSS
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