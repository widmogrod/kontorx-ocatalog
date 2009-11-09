<?php
class Catalog_Bootstrap extends Zend_Application_Module_Bootstrap {
	public function initResourceLoader() {
		$this->getResourceLoader()
			->addResourceTypes(array(
				'observer' => array(
					'namespace' => 'Model_Observer',
					'path'      => 'models/Observer',
				))
			);
	}
	
	public function _initModuleIncludePath() {
		$module = $this->getModuleName();
		$path = APP_MODULES_PATHNAME . strtolower($module) . '/models/';
		set_include_path($path . PATH_SEPARATOR . get_include_path());
	}
}