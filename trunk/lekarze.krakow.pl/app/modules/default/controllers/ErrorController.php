<?php
class Default_ErrorController extends Zend_Controller_Action {

	/**
	 * @var string
	 */
	public $ip;

	public function init() {
		$this->ip = KontorX_Util_Functions::getIP();
	}

    /**
     * Przechywytywanie błędów
     */
    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        /* @var $exception Exception */
        $exception = $errors->exception;

        $message = sprintf('%s :: %s (%d), [%s] [%s] %s',
        	get_class($exception),
        	$exception->getMessage(),
        	$exception->getLine(),
        	getenv('REQUEST_URI'),
        	$this->ip,
        	$exception->getTraceAsString());

        Zend_Registry::get('logger')->log($message, Zend_Log::CRIT);

		$this->_redirect('/');
    }

	/**
	 * Użytkownik jest zalogowany ale ma niewystarczające uprawnienia!
	 */
    public function privilegesAction() {
    	$message = sprintf('%s [%s] [%s] [%s]',
    		__FUNCTION__,
    		$this->ip,
    		getenv('REQUEST_URI'),
    		getenv('HTTP_FEREFER'));
    	
        Zend_Registry::get('logger')->log($message, Zend_Log::CRIT);
    }
}

