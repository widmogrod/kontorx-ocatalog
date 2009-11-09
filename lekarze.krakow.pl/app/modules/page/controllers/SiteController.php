<?php
class Page_SiteController extends KontorX_Controller_Action {
	public $skin = array(
		'layout' => 'admin_page',
		'config' => array(
			'filename' => 'backend_config.ini')
    );

	public function indexAction() {
		$this->view->form = new Page_Form_Add();
	}
}