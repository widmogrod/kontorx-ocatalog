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
	public function send($data, $html, array $emails = null) {
		$config = $this->_config;

		$mail = new Zend_Mail();
		$mail->addTo($data['email']); // do osoby wysyłającej
		$mail->addCc($config->to); // kopia do redakcji
		$mail->setFrom($config->from); // no-reply
		$mail->setBodyHtml($html, 'utf-8');

		if (is_array($emails)) {
			// dodaj listę maili
			array_map(array($mail, 'addTo'), $emails);
		}

		try {
			$mail->send();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Mail_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}
