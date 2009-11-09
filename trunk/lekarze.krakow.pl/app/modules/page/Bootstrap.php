<?php
class Page_Bootstrap extends Zend_Application_Module_Bootstrap {
	public function initResourceLoader() {
		$this->getResourceLoader()
			->addResourceTypes(array(
				'observer' => array(
					'namespace' => 'Model_Observer',
					'path'      => 'models/Observer',
				))
			);
	}
}