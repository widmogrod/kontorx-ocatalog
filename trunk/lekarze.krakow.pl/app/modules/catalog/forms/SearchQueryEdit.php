<?php
class Catalog_Form_SearchQueryEdit extends Catalog_Form_SearchQueryAdd {
	public function init() {
		parent::init();
        $this->getElement('action')->setLabel(Edytuj');
	}
}