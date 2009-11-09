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
 * Klasa sprawdza poprawność serii i numeru dowodu osobistego
 *
 * @package identificationNumbers
 * @version 1.1.2
 * @author Michał (kwiateusz) Kwiatek
 * @copyright 2008 Michał Kwiatek
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU Lesser General Public License
 */
class idcardValidator extends identificationNumberValidator
{
	/**
	 * Tablica wag dla serii i numeru dowodu
	 *
	 * @var array
	 */
	private $weight=array(
	7,
	3,
	1,
	7,
	3
	);
	
	/**
	 * Tablica liter z odpowiadającymi im wartościami numerycznymi
	 *
	 * @var array
	 */
	private $letterToNumber=array(
	'a' => 10,
  'b' => 11,
	'c' => 12,
	'd' => 13,
	'e' => 14,
	'f' => 15,
	'g' => 16,
	'h' => 17,
	'i' => 18,
	'j' => 19,
	'k' => 20,
	'l' => 21,
	'm' => 22,
	'n' => 23,
	'o' => 24,
	'p' => 25,
	'q' => 26,
	'r' => 27,
	's' => 28,
	't' => 29,
	'u' => 30,
	'v' => 31,
	'w' => 32,
	'x' => 33,
	'y' => 34,
	'z' => 35,
	);

	/**
	 * Kody błędów i ich komunikaty
	 * 
	 * @var array
	 */
	protected $errors=array(
	0=>"Podana seria i numer dowodu ma nieprawdłową ilosć znaków",
	1=>"Podana seria i numer dowodu jest nieprawidłowy",
	2=>"Podana seria i numer dowodu znajduje się na liście niepoprawnych numerów"
	);

	/**
	 * Ustawia serie, numer dowodu i wywołuje sprawdzenie jego poprawności
	 *
	 * @param strin $string
	 * @return void
	 */
	public function validate($string)
	{
		$string=(string)$string;
		$string= str_replace(' ', '', $string);
		$this->identifier=$string;
		$this->valid=false;
		
		if(self::isOnBlackList($string))
		{
			$this->handleError(2);
			return;
		}
		
  	if(strlen($string)!==9)
		{
				$this->handleError(0);
				return;
		}
		$this->identifier=$string;
		$this->doCheck();

	}

	/**
	 * Sprawdza poprawność serii i numeru dowodu
	 *
	 * return void
	 */
	protected function doCheck()
	{
		$controlSum=0;
		$partOne = substr($this->identifier, 0, 3);
		$partTwo = substr($this->identifier, 4, 5);

		$partOne = str_ireplace(array_keys($this->letterToNumber), $this->letterToNumber, $partOne);
		
    for ($i=0, $j=0; $j<=4 ;$i+=2, $j++ )
		{
			if($i<=4)
  	  {
				$controlSum += intval($partOne[$i].$partOne[$i+1])*$this->weight[$j];
   		}

			$controlSum += $partTwo[$j]*$this->weight[$j];
    }
    
		$controlNumber=$controlSum%10;

		if($this->identifier[3]==$controlNumber)
		{
			$this->valid=true;
			return;
		}
		$this->handleError(1);
		$this->valid=false;
		return;
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