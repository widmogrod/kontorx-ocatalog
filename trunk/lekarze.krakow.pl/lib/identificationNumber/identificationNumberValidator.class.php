<?php
/**
 * License
 *
 * This library is free software; you can redistribute it and/**
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Klasa bazowa dla wszystkich klas walidujących numenty identyfikacyjne.
 * Zawiera kilka podstawowych składowych i metod, których nie warto powtarzać
 * w kodzie potomnych.
 *
 * @package identificationNumbers
 * @version 1.1.2
 * @author Krzysztof (cysiaczek) Rak
 * @copyright 2008 Krzysztof Rak
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU Lesser General Public License
 */
abstract class identificationNumberValidator
{
	/**
	 * Numer podlagający walidacji
	 *
	 * @var string
	 */
	protected $identifier;

	/**
	 * Poprawność numeru
	 *
	 * @var bool
	 */
	protected $valid=false;
	
	/**
	 * Błedy w aktualnym żądaniu
	 * 
	 * @var array
	 */
	protected $requestErrors=array();
	
	/**
	 * Ilość błędów przy walidacji
	 *
	 * @var unknown_type
	 */
	protected $errorCounter=0;
	
	/**
	 * Tablica znanych, błędnych numerów
	 *
	 * @var array
	 */
	private static $BLACK_LIST=array();
	
	
	/**
	 * Rozpoczęcie walidacji
	 *
	 * @param mixed $number
	 * @return mixed
	 */
	abstract public function validate($number);
	
	/**
	 * Wewnętrzna metoda wykonująca czarną robotę.
	 * 
	 * @return void;
	 */
	abstract protected function doCheck();

	/**
	 * Po prostu inicjuje obiekt. Jeśli przekażemy parametr,
	 * wykona operacje sprawdzania numeru identyfikacyjnego
	 *
	 * @param string $string
	 */
	public function __construct($string=null)
	{
		if($string)
		{
			$this->validate($string);
		}
	}
	
	/**
	 * Zwraca walidowany numer
	 *
	 * @return mixed
	 */
	public function getNumber()
	{
		return $this->identifier;
	}
	
	/**
	 * Sprawdza, czy podany numer przeszedł pomyślnie weryfikację
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->valid;
	}
	
	/**
	 * Obsługuje błędy - wypełnia tablicę z błedami
	 *
	 * @param integer $errorCode
	 * @return void
	 */
	protected function handleError($errorCode)
	{
		if(isset($this->errors[$errorCode]))
		{
			$this->requestErrors[$this->errorCounter]['code']=$errorCode;
			$this->requestErrors[$this->errorCounter]['message']=$this->getErrorMessage($errorCode);
			$this->requestErrors[$this->errorCounter]['identifier']=$this->identifier;
			$this->errorCounter++;
		}
	}
	
	/**
	 * Zwraca tablicę błędów
	 * 
	 * @return array
	 */
	public function hasErrors()
	{
		if(!empty($this->requestErrors))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Zwraca tablicę błędów
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->requestErrors;
	}
	
	/**
	 * Zwraca ilość błędów w aktualnym obiekcie.
	 * Po wykonaniu metody reset() licznik jest wyzerowany
	 *
	 * @return integer
	 */
	public function getErrorCount()
	{
		return $this->errorCounter;
	}
	
	/**
	 * Zwraca kod pierwszego błędu
	 *
	 * @return integer
	 */
	public function getFirstErrorCode()
	{
		return $this->requestErrors[0]['code'];
	}
	
	/**
	 * Zwraca kod ostatniego błędu
	 *
	 * @return integer
	 */
	public function getLastErrorCode()
	{
		return $this->requestErrors[$this->errorCounter-1]['code'];
	}
	
	/**
	 * Zwraca komunikat błędu
	 *
	 * @param onteger $errorCode
	 * @return unknown
	 */
	public function getErrorMessage($errorCode)
	{
		return $this->errors[$errorCode];
	}
	
	/**
	 * Resetuje obiekt
	 *
	 * @return void
	 */
	public function reset()
	{
		$this->identifier=null;
		$this->errorCounter=0;
		$this->requestErrors=array();
	}
	
	/**
	 * Sprawdza, czy tablica czarnej listy została zainicjowana
	 *
	 * @param string $className
	 * @return bool
	 */
	public static function isBlackListed($className)
	{
		if(isset(self::$BLACK_LIST[$className]) && is_array(self::$BLACK_LIST[$className]) && !empty(self::$BLACK_LIST[$className]))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Sprawdza, czy numer znajduje się na czarnej liście
	 *
	 * @param string $item
	 * @param string $className
	 * @return bool
	 */
	public static function isOnBlackList($item, $className=null)
	{
		if(!self::isBlackListed($className))
		{
			return false;
		}

		if(in_array($item, self::$BLACK_LIST[$className]))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Ustawia czarna listę numerów w tablicy, kŧórej kluczem jest nazwa klasy
	 *
	 * @param array $blackList
	 * @param string $classname
	 */
	public static function setBlackList(array $blackList, $className)
	{
		self::$BLACK_LIST[$className]=$blackList;
	}
	
	/**
	 * Ta metoda pozwala wprowadzić większa ilośc numerów do sprawdzenia
	 *
	 * @param string $className
	 * @param array $numbers
	 * @return array
	 */
	public static function checkStack(array $numbers, $className=null)
	{
		$all=array();
		foreach($numbers as $number)
		{
			$all[]=new $className($number);
		}
		return $all;
	}
}
?>