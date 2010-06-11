<?php
class Agregator_IndexController extends Zend_Controller_Action
{
	public $skin = array(
		'layout' => 'agregator_index',
	);

	/**
	 * Wyswietlenie wszystkich wpisów
	 * @return void
	 */
	public function indexAction()
	{
		$page = $this->_getParam('page',1);
		$limit = 10; 
		
		$model = new Agregator_Model_Index();
		$this->view->rowset = $model->findAll($page, $limit);
		$this->view->paginator = $model->getPaginator($page, $limit); 
	}

	/**
	 * Wyswietlenie danej aktualności
	 * @return void
	 */
	public function displayAction()
	{
		$model = new Agregator_Model_Index();
		$this->view->row = $model->findById($this->_getParam('id'));
		$this->_helper->redirectUntil->gotoRouteUntil($this->view->row, array(), 'agregator-index');
	}
}