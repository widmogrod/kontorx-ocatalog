<?php
class Catalog_Model_Mail extends Promotor_Model_Abstract {

	/**
	 * @var Zend_Config
	 */
	protected $_config;
	
	/**
	 * @param Zend_Config $config
	 * @return Catalog_Model_Mail
	 */
	public function setConfig(Zend_Config $config) {
		$this->_config = $config;
		return $this;
	}
	
	/**
	 * @param array $data
	 * @param string $html
	 * @return void
	 */
	public function send($data, $html) {
		$config = $this->_config;
		
		$mail = new Zend_Mail();
		$mail->addTo($data['email']);
		$mail->addCc($config->to);
		$mail->setFrom($config->from);
		$mail->setBodyHtml($html, 'utf-8');

		try {
			$mail->send();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Mail_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}
