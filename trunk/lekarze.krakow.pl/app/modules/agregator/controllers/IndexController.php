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
		$model = new Agregator_Model_Index();
		$this->view->rowset = $model->findAll();
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