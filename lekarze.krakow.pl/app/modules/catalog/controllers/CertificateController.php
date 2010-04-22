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
		 ),
		 'list' => array(
	 		'layout' => 'admin_catalog',
			'config' => array(
				'filename' => 'backend_config.ini')
		 )
    );

    public $contexts = array(
    	'generate' => array('pdf'),
    	'show' => array('pdf','html')
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
	 * Wyświetlanie informacji o certyfikacie
	 * 
	 * @return void
	 */
	public function indexAction() {
		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$id = $rq->get('id');

		// sprawdzam czy jest włączony certyfikat
		$certificate = new Catalog_Model_Certificate();
		if (!$certificate->isEnabled($id))
		{
			$this->_redirect('/');
		}
		
		$model = new Catalog_Model_Catalog();
		$this->view->row = $model->findByIdCache($id);
	}

	/**
	 * Spis wszystkich gabinetów które maja nadany certyfikat
	 */
	public function listAction()
	{
		// sprawdzam czy jest włączony certyfikat
		$certificate = new Catalog_Model_Certificate();
		$select = $certificate->selectList();
		
		$options = $this->_helper->config('certificate.xml');
		
		$grid = KontorX_DataGrid::factory($select, $options->grid);
		$grid->setValues($this->_getAllParams());
		
		// setup grid paginatior
		$select = $grid->getAdapter()->getSelect();
		
		$paginator = Zend_Paginator::factory($select);
		$grid->setPaginator($paginator);
		$grid->setPagination($this->_getParam('page'), 20);

		$this->view->grid = $grid;
		$this->view->actionUrl = $this->_helper->url('list');
	}

	/**
	 * Pokaż certyfikat
	 */
	public function showAction()
	{
		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$id = $rq->get('id');
		
		$model = new Catalog_Model_Catalog();
		$this->view->row = $model->findByIdCache($id);
		$this->view->promoted = $model->isPromoCache($id);
		
		// sprawdzam czy jest włączony certyfikat
		$certificate = new Catalog_Model_Certificate();
		$this->view->certificated = $certificate->isEnabled($id);
	}
	
	/**
	 * Generuje pliki i dodatki do HTML związane z certyfikatem
	 * 
	 * @return void
	 */
	public function generateAction() 
	{
		/* @var $rq Zend_Controller_Request_Http */
		$rq = $this->getRequest();
		$id = $rq->get('id');
		
		$model = new Catalog_Model_Catalog();
		$this->view->row = $model->findByIdCache($id);
		$this->view->promoted = $model->isPromoCache($id);
		
		// sprawdzam czy jest włączony certyfikat
		$certificate = new Catalog_Model_Certificate();
		$this->view->certificated = $certificate->isEnabled($id);
	}

	/**
	 * Włącza lub wyłącza certyfikat
	 */
	public function changestateAction() 
	{
		// wyłącz widok
		$this->_helper->getHelper('ViewRenderer')->setNoRender(true);

		$model = new Catalog_Model_Certificate();
		
		$catalogId = $this->_getParam('id');
		$state = $this->_getParam('state');

		if ($model->isEnabled($catalogId))
		{
			$result = $model->disable($catalogId);
			$message = 'Certyfikat został włączony';
		} else {
			$result = $model->enable($catalogId);
			$message = 'Certyfikat został włączony';
		}
		
		/* @var $flashMessanger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessanger = $this->_helper->getHelper('flashMessenger');
		$flashMessanger->addMessage($message);
		
		$this->_helper->getHelper('Redirector')->goToUrl($_SERVER['HTTP_REFERER']);
	}
}