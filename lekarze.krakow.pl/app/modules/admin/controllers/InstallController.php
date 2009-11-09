<?php
class Admin_InstallController extends Promotor_Controller_Action {

	public $skin = array(
		// forsuje domyslny template i style na admin
		'template' => 'admin',
		'style' => 'default',
		// layout
		'layout' => 'admin_system',
		// rozszerzam konfiguracje
		'config' => array(
			'filename' => 'backend_config.ini'
		),

		'init' => array(
			'disableTemplate' => true
		)
	);

//	public $acl = array(
//		'init' => 'list',
//	);
	
	/**
	 * Akcja inicjująca.. tworzy bazę danych tylko z użytkownkiem!
	 * @return void
	 */
	public function initAction() {
		$this->_helper->viewRenderer->setNoRender();

		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$model = new System_Model_Update(APP_MODULES_PATHNAME);

		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');

		// zainicjuj użytkownika
		$model->update('user', true);

		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger,'addMessage'), $model->getMessages(true));

		$this->_helper->redirector->goTo('index','index','admin');
	}
}