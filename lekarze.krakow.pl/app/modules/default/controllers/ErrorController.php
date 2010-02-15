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

        $message = sprintf('%s :: %s (%d), %s',
        	get_class($exception),
        	$exception->getMessage(),
        	$exception->getLine(),
        	$exception->getTraceAsString());

        $log = Zend_Registry::get('logger');

        $log->log(sprintf('Request URI: %s', getenv('REQUEST_URI')), Zend_Log::DEBUG);
        $log->log(sprintf('HTTP Referer: %s', getenv('HTTP_FEREFER')), Zend_Log::DEBUG);
        $log->log(sprintf('Request IP: %s', $this->ip), Zend_Log::DEBUG);
        $log->log($message, Zend_Log::CRIT);

		$this->_redirect('/');
    }

	/**
	 * Użytkownik jest zalogowany ale ma niewystarczające uprawnienia!
	 */
    public function privilegesAction() {
    	$log = Zend_Registry::get('logger');

    	$log->log('Privilages! ... ->', Zend_Log::CRIT);
		$log->log(sprintf('Request URI: %s', getenv('REQUEST_URI')), Zend_Log::DEBUG);
        $log->log(sprintf('HTTP Referer: %s', getenv('HTTP_FEREFER')), Zend_Log::DEBUG);
        $log->log(sprintf('Request IP: %s', $this->ip), Zend_Log::DEBUG);

    }
}

