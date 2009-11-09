<?php
class Catalog_Form_SiteFrameEdit extends Catalog_Form_SiteFrameAdd {
	public function init() {
		parent::init();

		$alias = Zend_Controller_Front::getInstance()->getRequest()->getParam('alias');
		
		$exlude = array(
			'field' => 'alias',
			'value' => $alias
		);
		
		$this->getElement('submit')->setLabel('Edytuj');
		$this->getElement('alias')
			->removeValidator('Zend_Validate_Db_NoRecordExists')
			->addValidator(new Zend_Validate_Db_NoRecordExists('catalog_site_frame','alias', $exlude));
	}
}