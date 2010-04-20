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

    public $contexts = array(
    	'generate' => array('pdf')
    );
    
    public function init()
    {
    	/* @var $contextSwitch Zend_Controller_Action_Helper_ContextSwitch */
    	$contextSwitch = $this->_helper->getHelper('ContextSwitch');
    	
    	if (!$contextSwitch->hasContext('pdf'))
    	{
    		$contextSwitch->addContext('pdf', array(
	    		'suffix'    => 'pdf',
				'headers'   => array('Content-Type' => 'application/pdf'),
	    	));
    	}
    	$contextSwitch->initContext();
    }
    
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
		
		$model = new Catalog_Model_Catalog();

		$this->view->publicated = $model->isPromo($id);
		$this->view->row = $model->findByIdCache($id);
	}
}