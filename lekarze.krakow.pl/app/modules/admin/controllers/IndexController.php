<?php
require_once 'KontorX/Controller/Action.php';
class Admin_IndexController extends KontorX_Controller_Action {

	public $skin = array(
		'layout' => 'admin',
		'config' => array(
			'filename' => 'backend_config.ini')
	);
	
	public function indexAction() {}
}