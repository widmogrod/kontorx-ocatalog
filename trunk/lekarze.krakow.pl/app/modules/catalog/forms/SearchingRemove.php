<?php
class Catalog_Form_SearchingRemove extends Zend_Form {
	public function init() {
		$this->setMethod('post');
		$this->addElement('checkbox','delete', array(
			'label' => 'Usuń rekord',
			'description' => 'Usunięcie rekordu jest nieodwracalne',
			'required' => true,
			'validators' => array(
				'greaterThan' => array('validator' => new Zend_Validate_GreaterThan(0))
			)));

		$this->addElement('submit','Usuń', array('ignore' => true));
	}
}