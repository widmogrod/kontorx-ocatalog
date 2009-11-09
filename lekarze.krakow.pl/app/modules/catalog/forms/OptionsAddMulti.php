<?php
class Catalog_Form_OptionsAddMulti extends Zend_Form {

	public function init() {
		$this->setMethod('post');
		$this
			->addElement('submit','Zapisz')
			->addElement(new KontorX_Form_Element_Db_Select('catalog_id', array(
				'label' => 'Wizytówka',
				'required' => true,

				'firstNull' => true,
				'tableName' => 'catalog',
				'tableCols' => array('id','name'),
				'optionKey' => 'id',
				'optionValue' => 'name',
			)))
			->addElement(new KontorX_Form_Element_Db_MultiCheckbox('options', array(
				'label' => 'Gabinet oferuje',
				'required' => false,
				'registerInArrayValidator' => false,
			
				'firstNull' => true,
				'tableName' => 'catalog_options',
				'tableCols' => array('id','name'),
				'optionKey' => 'id',
				'optionValue' => 'name'
			)));
	}
}