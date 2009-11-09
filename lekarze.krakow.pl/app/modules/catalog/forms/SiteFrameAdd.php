<?php
class Catalog_Form_SiteFrameAdd extends Zend_Form {
	public function init() {
		$this->setMethod('post');
		
		$this
			->addElement('submit', 'submit', array(
				'label' => 'Zapisz',
				'ignore' => true
			))
			->addElement('text', 'alias', array(
				'label' => 'Alias',
				'required' => true,
				'validators' => array(
					'db' => new Zend_Validate_Db_NoRecordExists('catalog_site_frame','alias')
				),
				'filters' => array(
					new Zend_Filter_StringToLower(),
					new Zend_Filter_StripTags(),
					new KontorX_Filter_Word_Rewrite()
				)
			))
			->addElement('select', 'frame', array(
				'label' => 'Ramka serwisu',
				'required' => true,
				'multiOptions' => array(
					'YES' => 'Tak',
					'NO' => 'Nie'
				)
			))
			->addElement('text', 'uri', array(
				'label' => 'Adres strony',
				'required' => true,
			))
			->addElement('text', 'meta_title', array(
				'label' => 'Tytuł strony',
				'required' => true,
				'filters' => array(
					new Zend_Filter_StripTags()
				)
			))
			->addElement('textarea', 'meta_keywords', array(
				'label' => 'Słowa kluczowe',
				'required' => true,
				'filters' => array(
					new Zend_Filter_StripTags()
				)
			))
			->addElement('textarea', 'meta_description', array(
				'label' => 'Opis strony',
				'required' => true,
				'filters' => array(
					new Zend_Filter_StripTags()
				)
			));
	}
}