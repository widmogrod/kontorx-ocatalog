<?php
class Catalog_Form_SearchingEdit extends Catalog_Form_SearchingAdd {
	public function init() {
		parent::init();
        $this->getElement('action')->setLabel(Edytuj');
	}
}