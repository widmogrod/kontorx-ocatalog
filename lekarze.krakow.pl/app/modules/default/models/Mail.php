<?php
class Default_Model_Mail extends Promotor_Model_Abstract {

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
	 * @var Zend_Mail
	 */
	protected $_mail;
	
	/**
	 * @param Zend_Mail $mail 
	 */
	public function setMail(Zend_Mail $mail) {
		$this->_mai = $mail;
	}
	
	/**
	 * @return Zend_Mail
	 */
	public function getMail() {
		if (null === $this->_mail) {
			$this->_mail = new Zend_Mail();
		}
		return $this->_mail;
	}
	
	/**
	 * @param array $data
	 * @param string $html
	 * @return void
	 */
	public function send($data, $html) {
		$mail = $this->getMail();
		$mail->setSubject($this->_config->subject);
		$mail->addTo($data['email']);
		$mail->addCc($this->_config->to);
		$mail->setFrom($this->_config->from);
		$mail->setBodyHtml($html, 'utf-8');

		try {
			$mail->send();
			$this->_setStatus(self::SUCCESS);
		} catch (Zend_Mail_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
	
	/**
	 * @param Zend_Form $form
	 */
	public function addAttachmentFromForm(Zend_Form $form) {
		$mail = $this->getMail();

		foreach ($form->getElements() as $element) {
			if ($element instanceof Zend_Form_Element_File) {
				/* @var $element Zend_Form_Element_File */
				$name = $element->getName();
				if ($element->getTransferAdapter()->isUploaded($name)) {
					$fileName = $element->getFileName();
					$body = file_get_contents($fileName);

					if (function_exists('mime_content_type')) {
						$mimeType = mime_content_type($fileName);
					} elseif (class_exists('finfo')) {
						$fi = new finfo(FILEINFO_MIME);
				      	$mimeType = $fi->buffer($body);
					} else {
						$mimeType    = Zend_Mime::TYPE_OCTETSTREAM;
					}

					$attachment = $mail->createAttachment($body, $mimeType);
				}
			}
		}
	}
}