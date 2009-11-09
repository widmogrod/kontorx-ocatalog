<?php
require_once 'KontorX/Controller/Action.php';
class Catalog_AdminController extends KontorX_Controller_Action {
	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	public function indexAction() {
		$this->_forward('list','catalog');
	}

	/**
	 * Czyszczenie miniaturek
	 */
	public function cleancacheAction() {
		$config = $this->_helper->config('config.ini');
		$path = $config->path->upload;
		
		$path = $this->_helper->system()->getPublicHtmlPath($path);

		$errors = 0;

		$iterator = new RecursiveDirectoryIterator($path);
		$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
		while ($iterator->valid()) {
			// wszystkie pliki które znajdują się głębiej
			if ($iterator->getDepth() > 0) {
				$current = $iterator->current();
				if ($current->isFile()) {
					if (!@unlink($current->getPathname())) {
						++$errors;
					}
				}
			}
			$iterator->next();
		}

		if ($errors < 1) {
			$message = "Cache został wyczyszczony";
		} else {
			$message = "Cache NIE został wyczyszczony w pełni! ($errors)";
		}

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->flashMessenger->addMessage($message);
		$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
	}

	public function cleancachedataAction() {
		require_once 'catalog/models/Catalog.php';
		$catalog = new Catalog();
		
		try {
			$catalog->clearCacheMapData(PUBLIC_PATHNAME);
			$message = "Cache został wyczyszczony";
		} catch (Catalog_Exception $e) {
			$message = $e->getMessage();
		}
		
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->flashMessenger->addMessage($message);
		$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
	}
}