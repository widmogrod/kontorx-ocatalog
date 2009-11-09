<?php
class Catalog_MailController extends Zend_Controller_Action {
	
	public $skin = array(
//		// forsuje domyslny template i style na admin
//		'template' => 'admin',
//		'style' => 'default',
//		// layout
//		'layout' => 'admin_catalog',
//		// rozszerzam konfiguracje
//		'config' => array(
//			'filename' => 'backend_config.ini'
//		),

		// specjalizuje template dla akcji
		'send' => array(
			'layout' => 'catalog_mail_send'
		)
	);

	public function sendAction() {
		$catalogId = $this->_getParam('id');

		$model = new Catalog_Model_Catalog();
		$data = $model->findByIdCache($catalogId);
		$this->view->data = $data;

		$valid = new Zend_Validate_EmailAddress();
		
    	if (!$valid->isValid(@$data['email'])) {
			$this->_forward('show','index','catalog', array('id' => $catalogId));
			return;
		}

		$rq = $this->getRequest();

		$form = new Catalog_Form_Mail();

		if (!$rq->isPost()) {
			$this->view->form = $form;
			return;
		}

		if (!$form->isValid($rq->getPost())) {
			$this->view->form = $form;
			return;
		}

		// konfiguracja.. gdzie przesyłać maile
		$config = $this->_helper->config('mail.ini');
		$values = $form->getValues();

		// renderowanie treści maila
		$this->view->assign($values);
		$html = $this->view->render('mail/send-mail.phtml');

		// wysyłanie maila
		$model = new Catalog_Model_Mail();
		$model->setConfig($config);
		$model->send($values, $html);

		/* @var $flashMessanger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessanger = $this->_helper->getHelper('flashMessenger');

		if (Catalog_Model_Mail::SUCCESS === $model->getStatus()) {
			// wiadomość o powodzeniu akcji
			$message = sprintf('Wiadomość została wysłana do: %s', $data['name']);
			$flashMessanger->addMessage($message);

			// przekierowanie do strony wizytówki
			$this->_helper->redirector->goToUrl(
				$this->_helper->url->url(array('id' => $catalogId),'catalog-show')
			);
		} else {
			// powórt do formularza
			$this->view->form = $form;
			$message = 'Wiadomość NIE została wysłana!';
			$flashMessanger->addMessage($message);

			// dodawanie wiadomości z modelu @todo dodać do logowania
			array_map(array($flashMessanger, 'addMessage'), $model->getMessages(true));
		}
	}
}