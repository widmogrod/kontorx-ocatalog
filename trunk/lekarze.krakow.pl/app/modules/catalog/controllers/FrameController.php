<?php
class Catalog_FrameController extends Zend_Controller_Action {
	public $skin = array(
		'layout' => 'catalog_frame'
	);

	public function indexAction() {
		$this->_getParam('id');
	}
}