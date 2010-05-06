<?php
/**
 * API REST
 * 
 * @author $Author$
 * @version $Id$
 */
class Api_RestController extends Zend_Controller_Action
{
	/**
	 * Uwierzytelnienie działa na podstawie HTTP Authorization: Basic
	 * Klucz jest zakodowany login & hasło użytkownika
	 * 
	 * @return void
	 */
	public function init()
	{
		// wyłącz widoki
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		if (!isset($_SERVER['PHP_AUTH_USER']) 
			// to jednoczesnie wyklucza kasło typu -1, 0 ...
			|| !isset($_SERVER['PHP_AUTH_PW']))
		{
		    header('WWW-Authenticate: Basic realm="'. CATALOG_HOSTNAME .'"');
		    header('HTTP/1.0 401 Unauthorized');
		    exit('{"error": {"name": "unauthorized"}}');
		}

		$model = new User_Model_Auth();
		$result = $model->isValid($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

		if (!$result)
		{
			header('WWW-Authenticate: Basic realm="'. CATALOG_HOSTNAME .'"');
			header('HTTP/1.0 401 Unauthorized');
			exit('{"error": {"name": "bad_authenication"}}');
		}
		
		/**
		 * TODO: W przysłości mozna dodac sprawdzanie dostepu do pewnych metod a API
		 * ale nie wydaje mi się to teraz najwazniejsze :). 6 maj 2010
		 */
	}

	/**
	 * @return void
	 */
	public function indexAction()
	{
		$server = new Zend_Rest_Server();
		$server->handle();
	}

	/**
	 * API wyszukiwania ma zaimplementowane metody
	 * - GET lucene
	 * 
	 * @return void
	 */
	public function searchAction()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Api_Model_SearchProxy');
		$server->handle();
	}
}