<?php
class Catalog_CertificateController extends KontorX_Controller_Action {

	 /**
	  * @var array
	  */
	 public $skin = array(
		'layout' => 'catalog_certificate',
	 	'generate' => array(
	 		'layout' => 'admin_catalog',
			'config' => array(
				'filename' => 'backend_config.ini')
		 )
    );

	/**
	 * @return void
	 */
	public function indexAction() {
		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$id = $rq->get('id');

		$model = new Catalog_Model_Catalog();
		if (!$model->isPromoCache($id)) {
			$this->_helper->redirector->goToUrlAndExit('/');
			return;
		}

		/* @var $flashMessanger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessanger = $this->_helper->getHelper('flashMessenger');
		array_map(array($flashMessanger, 'addMessage'), $model->getMessages(true));

		$this->view->row = $model->findByIdCache($id);
	}

	/**
	 * Generuje linki do gabinet oferuje
	 * @return void
	 */
	public function generateAction() {
		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$id = $rq->get('id');
		
		/* @var $flashMessanger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessanger = $this->_helper->getHelper('flashMessenger');
		
		$model = new Catalog_Model_Catalog();
		if (!$model->isPromoCache($id)) {
			$flashMessanger->addMessage('Nie można wygenerować certyfikatu dla wizytówki, która nie jest w promocji!');
			$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
			return;
		}

		$this->view->row = $model->findByIdCache($id);
	}
}