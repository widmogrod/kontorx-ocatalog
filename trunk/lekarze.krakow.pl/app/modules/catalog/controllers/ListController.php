<?php
class Catalog_ListController extends Zend_Controller_Action {

	public $skin = array(
		'layout' => 'catalog_list'
	);
	
	public function indexAction() {
		$config = $this->_helper->config('list.ini');

		$page = $this->_getParam('page', 1);
		$rowCount = $config->rowCount;

		$model = new Catalog_Model_CatalogList();
		
		if ($this->_hasParam('url')) {
			$this->view->district = $this->_getParam('url');
			$data = $model->findAllByDistrictCache($this->view->district, $page, $rowCount);
		} else {
			$data = $model->findAllCache($page, $rowCount);
		}

		if (!is_array($data)) {
			$this->_helper->viewRenderer->render('index.no.found');
			return;
		}

		list ($rowset, $select, $row) = $data;
		$this->view->row = $row;
		$this->view->rowset = $rowset;

		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($rowCount);
		$this->view->paginator = $paginator;
	}

	/**
	 * Autocomplete adres
	 * @return void
	 */
	public function autocompleteadressAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		if (strlen($q = $this->_getParam('q')) > 0) {
			$model = new Catalog_Model_CatalogList();
			$rowset = $model->findAdress($q);

			print Zend_Json::encode($rowset);
		}
	}

	/**
	 * Autocomplete adres
	 * @return void
	 */
	public function gmapAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();

		$model = new Catalog_Model_CatalogList();
		$rowset = $model->findAllGMapCache();

		$json = Zend_Json::encode($rowset);

		$model->saveGMapCache($json);

		print $json;
	}

	/**
	 * Wyczyść cheche
	 * @return void
	 */
	public function cleargmapcacheAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		$model = new Catalog_Model_CatalogList();
		$model->clearGMapCache();

		$model->clearCacheSitemap();
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger,'addMessage'), $model->getMessages());
		
		$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
	}

	/**
	 * Autocomplete adres
	 * @return void
	 */
	public function kmlAction() {
		$this->_helper->layout->disableLayout();

		$model = new Catalog_Model_CatalogList();
		$this->view->rowset = $model->findAllKml();
	}

	/**
	 * @return void
	 */
	public function sitemapAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$model = new Catalog_Model_CatalogList();
		$rowset = $model->findAllSitemap();

		$container = new Zend_Navigation($rowset);
		$render = $this->view->getHelper('navigation')->sitemap($container)->render();

		$model->saveCacheSitemap($render);
		
		print $render;
	}
}