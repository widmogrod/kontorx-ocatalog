<?php
class Api_Bootstrap extends Zend_Application_Module_Bootstrap 
{
	public function initResourceLoader() 
	{
		// reset resource types ...
		// nie potrzebuję Rorm_, DbTable_ itd..
		$this->getResourceLoader()
			->setResourceTypes(array(
				'observer' => array(
					'namespace' => 'Model_',
					'path'      => 'models/',
				))
			);
	}
}