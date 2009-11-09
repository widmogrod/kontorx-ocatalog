<?php
class Catalog_Form_Mail extends Zend_Form {
	public function init() {
		$this->setMethod('post');
		
		$registry = Zend_Registry::getInstance();
		
		$this
			->addElement('textarea','content', array(
				'required' => true,
				'value' => 'Wpisz treść wiadomości..',
				'validators' => array(
					new Zend_Validate_StringLength(20)
				),
				'filters' => array(
					'StripTags',
					new KontorX_Filter_MagicQuotes()
				)
			))
			->addElement('text','email', array(
				'Label' => 'Twój adres e-mail:',
				'required' => true,
				'validators' => array(
					'EmailAddress'
				),
				'filters' => array(
					'StripTags'
				)
			))
			->addElement('captcha', 'recaptcha', array(
				'captcha' 	=> 'ReCaptcha',
				'label'		=> 'Kod weryfikujący',
				'captchaOptions' => array(
					'pubkey' 	=> ReCAPTCHA_PUBLIC_KEY,
					'privkey' 	=> ReCAPTCHA_PRIVATE_KEY
				)
			))
			->addElement('submit','wyślij', array(
				'Label' => 'Wyślij wiadomość',
				'ignore' => true
			));
			
			
		$this->getElement('content')->removeDecorator('Label');
	}
}