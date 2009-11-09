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
 * Klasa sprawdza poprawność numeru PESEL.
 * Dodatkowo umożliwia sprawdzenie płci osoby ukrywającej się za tym numerem i porównanie
 * daty z peselu z inną, przekazaną datą.
 *
 * @package identificationNumbers
 * @version 1.1.2
 * @author Krzysztof (cysiaczek) Rak
 * @copyright 2008 Krzysztof Rak
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU Lesser General Public License
 */
class peselValidator extends identificationNumberValidator
{
	/**
	 * Skrócona tablica wag dla numeru PESEL
	 *
	 * @var array
	 */
	private $lightWeight=array(
	1,
	3,
	7,
	9
	);
	
	/**
	 * Kody stuleci
	 * 
	 * @var array
	 */
	private $centuries=array(
	0, // 1900
	2, // 2000
	4, // 2100
	6, // 2200
	8  // 1800 
	);
	
	/**
	 * Pierwsze dwie liczby roku dla każdego stulecia
	 * 
	 * @var array
	 */
	private $centuryDetails=array(
	0=>19,
	2=>20,
	4=>21,
	6=>22,
	8=>18 
	);
	
	/**
	 * Zawiera tablicę z datą w kolejności
	 * Rok
	 * Miesiąc
	 * Dzień
	 * 
	 * @var array
	 */
	private $date=array();
	
	/**
	 * Kody błędów i ich komunikaty
	 * 
	 * @var array
	 */
	protected $errors=array(
	0=>"Podany numer PESEL ma nieprawdłową ilosć znaków",
	1=>"Podany numer PESEL jest nieprawidłowy",
	2=>"Podany numer PESEL znajduje się na liście niepoprawnych numerów"
	);

	/**
	 * Ustawia numer PESEL i wywołuje sprawdzenie jego poprawności
	 *
	 * @param strin $string
	 * @return void
	 */
	public function validate($string)
	{
		$string=(string)$string;
		$this->identifier=$string;
		$this->valid=false;
		
		if(self::isOnBlackList($string))
		{
			$this->handleError(2);
			return;
		}
		
		if(is_numeric($string))
		{
			if(strlen($string)!==11)
			{
				$this->handleError(0);
				return;
			}
			$this->identifier=$string;
			$this->doCheck();
		}
	}

	/**
	 * Sprawdza poprawność numeru PESEL
	 *
	 * return void
	 */
	protected function doCheck()
	{
		$controlSum=0;
		for($i=0; $i<=9; ++$i)
		{
			$controlSum=($controlSum+$this->identifier[$i]*$this->lightWeight[$i%4])%10;
		}
		$controlNumber=(10-$controlSum)%10;

		if($this->identifier[10]==$controlNumber && $this->validBirthDate())
		{
			$this->valid=true;
			return;
		}
		$this->handleError(1);
		$this->valid=false;
		return;
	}

	/**
	 * Zwraca numer PESEL
	 * 
	 * return string
	 */
	public function getPesel()
	{
		return $this->getNumber();
	}
	
	/**
	 * Sprawdza, czy numer PESEL należy do mężczyzny
	 *
	 * @return unknown
	 */
	public function isMale()
	{
		if($this->identifier[9]%2!==0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Sprawdza, czy numer PESEL należy do kobiety
	 *
	 * @return bool
	 */
	public function isFemale()
	{
		return !$this->isMale();
	}
	
	/**
	 * Sprawdza, czy osoba kryjąca się za numerwm PESEL jest pełnoletnia
	 * 
	 * @return bool
	 */
	public function isAdult()
	{
		if(((strtotime("now") - strtotime($this->getBirthDate()))/31536000)>=18)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Oblicza i zwraca datę urodzenia z pełnym rokiem w formacie YYYYMMDD
	 * 
	 * @return string; 
	 */
	public function getBirthDate()
	{		
		if(!empty($this->date))
		{
			return implode("", $this->date);
		}
		$rawDate=substr($this->identifier, 0, 5);
		$peselArray=str_split($this->identifier);
		$rawMonth=$peselArray[2].$peselArray[3];

		$month=$this->getBirthMonth($rawMonth);
		$year=$peselArray[0].$peselArray[1];
		$year=$this->getYear($this->century).$year;
		$day=$peselArray[4].$peselArray[5];
		
		/*
		 * Cache do tablicy
		 */
		$this->date['year']=$year;
		$this->date['month']=$month;
		$this->date['day']=$day;
		
		$date=$year.$month.$day;
		return $date;
	}
	
	/**
	 * Oblicza i zwraca miesiąc pochodzący z numeru PESEL
	 * 
	 * @return string
	 */
	private function getBirthMonth($string)
	{
		$number=(integer)$string[0];
		foreach($this->centuries as $code)
		{
			$test=$number-$code;
			if($test<=1 && !($test<0))
			{
				$this->century=$code;
				return $month=$test.$string[1];
			}
		}
		return $string;
	}
	
	/**
	 * Sprawdza, czy data jest poprawną datą gregoriańską
	 * 
	 * @return bool
	 */
	private function validBirthDate()
	{
		$this->getBirthDate();
		return checkdate($this->date['month'], $this->date['day'], $this->date['year']);
	}
	
	/**
	 * Sprawdza, czy przekazana jako argument data jest tożsama z tą z numeru PESEL
	 * Data musi mieć format YYYYMMDD
	 * 
	 * @param mixed (string/long)
	 * @return bool
	 */
	public function matchDate($date)
	{
		if($this->getBirthDate()==$date)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Zwraca dwie pierwsze liczby z daty stulecia wg.zadanego kodu.
	 * 
	 * @param integer
	 * @return integer
	 */
	private function getYear($centuryCode)
	{
		return $this->centuryDetails[$centuryCode];
	}

	/**
	 * Ta metoda pozwala wprowadzić większa ilośc numerów do sprawdzenia
	 *
	 * @param array $numbers
	 * @return array
	 */
	public static function checkStack(array $numbers, $nothingSpecial=null)
	{
		return parent::checkStack(__CLASS__, $numbers);
	}
	
	/**
	 * Dodaje czarną listę do klasy
	 *
	 * @param array $blackList
	 * @return void
	 */
	public static function addBlackList(array $blackList)
	{
		parent::setBlackList($blackList, __CLASS__);
	}
	
	/**
	 * Sprawdza, czy dla tej klasy została ustawiona czarna lista
	 *
	 * @return bool
	 */
	public static function hasBlackList()
	{
		return parent::isBlackListed(__CLASS__);
	}
	
	/**
	 * Sprawdzam czy element (numer) znajduje się na czarnej liście
	 *
	 * @param string $item
	 * @param string $className
	 * @return bool
	 */
	public static function isOnBlackList($item, $className=__CLASS__)
	{
		return parent::isOnBlackList($item, $className);
	}
	
}
?>