<?php
require_once 'KontorX/Form/DbTable.php';

class Catalog_Form_ManagementCatalogEditForm extends KontorX_Form_DbTable {
	public function postInit() {
		$this->setAttrib('class','form_edit');

		$this
			->clearDecorators()
		 	->addDecorator('FormElements')
			->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'zend_form'))
			->addDecorator('Form');

		$this->setElementDecorators(array(
			'ViewHelper',
			'Errors',
			array('Description', array('tag' => 'p', 'class' => 'description')),
			array('HtmlTag', array('tag' => 'div')),
			array('Label', array('tag' => 'dt'))
		));

		$this->setDefaultDisplayGroupClass('Catalog_Form_DisplayGroup');

		$this->removeElement('user_id');

		$this->addDisplayGroup(array(
			'name','catalog_type_id','description','info','catalog_image_id'),'defaultgroup');
		
		$this->addDisplayGroup(array(
			'adress','postcode','www','email','contact'),'contactgroup');
		
		$this->addDisplayGroup(array(
			'lat', 'lng', 'catalog_district_id'),'map-group');
		
		$this->addDisplayGroup(array(
			'meta_title', 'meta_description','meta_keywords'),'metagroup'
		);
	}
}