<?php
class Catalog_Form_SearchFindEdit extends Catalog_Form_SearchFindAdd {
	public function init() {
		parent::init();
        $this->getElement('action')->setLabel(Edytuj');
	}
}