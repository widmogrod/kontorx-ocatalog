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
 * Klasa sprawdza poprawność numerów REGON (7, 9, 14) 
 *
 * @package identificationNumbers
 * @version 1.1.2
 * @author Krzysztof (cysiaczek) Rak
 * @copyright 2008 Krzysztof Rak
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU Lesser General Public License
 */
class regonValidator extends identificationNumberValidator
{

	/**
	 * Tablica wag dla numerów REGON
	 *
	 * @var array
	 */
	private $weights=array(
		7=>array(2, 3, 4, 5, 6, 7), // REGON 7
		9=>array(8, 9, 2, 3, 4, 5, 6, 7), // REGON 9
		14=>array(2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8) // REGON 14
	);

	/**
	 * Numer REGON do aktualnego przetwarzania
	 * 
	 * @var string
	 */
	private $rawREGON;
	
	/**
	 * Wersja numeru REGON (7, 9, 14)
	 * 
	 * @var integer
	 */
	private $version;
	
	/**
	 * Określa, czy podany REGON 14 jest prawidłowy wg wag dla REGON 14.
	 * 
	 * @var bool
	 */
	private $valid14=false;

	/**
	 * Kody błędów i ich komunikaty
	 *
	 * @var array
	 */
	protected $errors=array(
	0=>"Podany numer REGON ma nieprawdłową ilosć znaków",
	1=>"Podany numer REGON jest nieprawidłowy",
	2=>"Podany numer REGON znajduje się na liście niepoprawnych numerów",
	3=>"Podany numer REGON jest nieprawidłowy, ale przeszedł pomyślnie walidację dla REGON14"
	);

	/**
	 * Ustawia numer REGON i wywołuje sprawdzenie jego poprawności
	 *
	 * @param string $string
	 * @return void
	 */
	public function validate($string)
	{
		$string=(string)$string;
		$this->valid=false;
		$this->version=9;
		$this->identifier=$string;
		
		$this->rawREGON=$string;
		$length=strlen($string);

		if(self::isOnBlackList($this->rawREGON))
		{
			$this->handleError(2);
			return;
		}
		
		if($length==7)
		{
			$this->version=7;
		}
		elseif($length==14)
		{
			$this->version=14;
		}

		if(is_numeric($this->rawREGON))
		{
			$strlen=strlen($this->rawREGON);
			if($strlen!==$this->version)
			{
				$this->handleError(0);
				return;
			}

			$this->identifier=$string;
			$this->doCheck();
			
			/*
			 * REGON 14 ma błąd - jedna z wag wynosi 0.
			 * Aby wyeliminować czeskie błędy polegające na przestawieniu cyferek
			 * sprawdzany jest jeszcze na poprawnośc swojej 9-o cyfrowej postaci.
			 * Może zatem być poprawnym numerem 14-o cyfrowym, ale jedneocześnie niepoprawnym
			 * 9-o cyfrowym.
			 */
			if($this->isValid() && $this->version==14)
			{
				$this->version=9;
				$this->valid=false;
				$this->valid14=true;

				$this->originalREGON=$this->rawREGON;
				$this->rawREGON=substr($this->rawREGON, 0, 9);
				$this->doCheck();
				$this->version=14;
				if(!$this->isValid() && $this->isValid14())
				{
					$this->handleError(3);
				}
			}
		}
	}

	/**
	 * Sprawdza poprawność numeru REGON
	 *
	 * @return void
	 */
	protected function doCheck()
	{
		$loop=$this->version-2;
		$checkOffset=$this->version-1;
		$controlSum=0;
		
		for($i=0; $i<=$loop; ++$i)
		{
			$controlSum=$controlSum+($this->rawREGON[$i]*$this->weights[$this->version][$i]);
		}
		$controlNumber=$controlSum%11;
		
		/*
		 * Zabezpiecznie przed niepoprawną cyfrą kontrolną
		 */
		if($controlNumber==10)
		{
			$controlNumber=0;
		}

		if($this->rawREGON[$checkOffset]==$controlNumber)
		{
			$this->valid=true;
			return;
		}
		$this->handleError(1);
		$this->valid=false;
		return;
	}

	/**
	 * Zwraca numer REGON
	 *
	 * @return string
	 */
	public function getRegon()
	{
		return $this->getNumber();
	}
	
	/**
	 * Sprawdza, czy REGON14 jest poprawny wg wag dla tej wersji.
	 * NIEKONIECZNIE MOŻE TO BYĆ POPRAWNY REGON!!!
	 * 
	 * @return bool
	 */
	public function isValid14()
	{
		return $this->valid14;
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