<?php
class Catalog_Form_CatalogTimeEdit extends Catalog_Form_CatalogTimeAdd {

	public function init() {
		parent::init();

        $this->getElement('action')->setLabel('Edytuj');
	}
}