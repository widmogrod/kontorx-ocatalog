<?php
class User_Model_Auth
{
	// 'przyprawy' dla haskowania kluczy
	const SALT_PASSWORD = "!@#Kn_)<>?@!#_   >_)@#NJOIksa|}{P";
	const SALT_KEY 		= "!@#><M@21.3,m .3,m12 3.,m@!#.,m231.1 2.3,m";

	/**
	 * Generowanie hasła
	 *
	 * @param string $username
	 * @param string $password
	 * @return string
	 */
	public static function saltPassword($username, $password) {
		return md5($username . self::SALT_PASSWORD . $password);
	}

	protected $_authAdapter;
	
	public function getAuthAdapter()
	{
		if (null === $this->_authAdapter)
		{
			$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();

			$this->_authAdapter = new Zend_Auth_Adapter_DbTable($adapter);
			$this->_authAdapter->setTableName('user');
			$this->_authAdapter->setIdentityColumn('email');
			$this->_authAdapter->setCredentialColumn('password');
	
			// tylko uzytkownicy którzry potwierdzili rejestrację
			$this->_authAdapter->setCredentialTreatment('? AND registered = 1');
		}
		return $this->_authAdapter;
	}
	
	/**
	 * @param string $usernames - username is email address!
	 * @param string $password
	 * @return Zend_Auth_Result
	 */
	public function authorization($username, $password)
	{
		// przyprawiamy hasło
		$password = self::saltPassword($username, $password);

		$authAdapter = $this->getAuthAdapter();
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);

		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($authAdapter);

		return $result;
	}
	
	/**
	 * Sprawdzana jest tylko poprawnośc danych!
	 * 
	 * Różnica pomiędzy metodą {@see authorization()}
	 * polega na tym że globalny obiekt {@see Zend_Auth}
	 * nie jest nadpisywany.. 
	 * 
	 * @param string $usernames - username is email address!
	 * @param string $password
	 * @return Zend_Auth_Result
	 */
	public function isValid($username, $password)
	{
		// przyprawiamy hasło
		$password = self::saltPassword($username, $password);

		$authAdapter = $this->getAuthAdapter();
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);
		return $authAdapter->authenticate()->isValid();
	}
}