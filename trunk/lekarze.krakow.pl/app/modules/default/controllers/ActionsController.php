<?php
require_once 'KontorX/Controller/Action.php';
class Default_ActionsController extends KontorX_Controller_Action {

    /**
     * Formularz kontaktowy
     * @return void
     */
    public function contactAction() {
        $config = $this->_helper->config('actions.ini');
        $form = new Zend_Form($config->form->kontakt);

        if ($form->translatorIsDisabled()) {
        	$form->getTranslator()
        		->setLocale($this->getFrontController()->getParam('locale'));
        }

        if (!$this->_request->isPost()) {
            $this->view->form = $form->render();
            return;
        }

        if (!$form->isValid($_POST)) {
            $this->view->form = $form->render();
            return;
        }

        // przygotowanie danych
        $data = $form->getValues();
        $data = get_magic_quotes_gpc() ? array_map('stripslashes', $data) : $data;

		// renderowanie treści maila
		$this->view->assign($data);
		$html = $this->view->render('actions/send-mail.phtml');

    	// wysyłanie maila
		$model = new Default_Model_Mail();
		$model->setConfig($config->kontakt);
		$model->send($data, $html);

		/* @var $flashMessanger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessanger = $this->_helper->getHelper('flashMessenger');

		if (Default_Model_Mail::SUCCESS === $model->getStatus()) {
			// wiadomość o powodzeniu akcji
			$message = sprintf('Wiadomość została wysłana do: %s', $data['email']);
			$flashMessanger->addMessage($message);

			// przekierowanie do strony wizytówki
			$this->_helper->redirector->goToUrl(getenv('HTTP_REFERER'));
		} else {
			// powórt do formularza
			$this->view->form = $form;
			$message = 'Wiadomość NIE została wysłana!';
			$flashMessanger->addMessage($message);

			// dodawanie wiadomości z modelu @todo dodać do logowania
			array_map(array($flashMessanger, 'addMessage'), $model->getMessages(true));
		}
    }

    /**
     * Zmiana lokalizacji
     *
     */
    public function languageAction() {
        $locale = $this->_getParam('locale');
        if (Zend_Locale::isLocale($locale)) {
            // ustawienie ciasteczka z rozpoznawaniemjezyka na 2tyg.
            setcookie('locale',$locale, time()+122000, '/');
        }

        $referer = getenv('HTTP_REFERER');
        if (null === $referer) {
            $this->_forward('index');
        } else {
            $this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
        }
    }
}