<?php
/**
 * Najprostsze użycie klasy
 * 160152906
 * 100152912
 * 240208157
 * 
 * 150172561
 * 12345678512347
 * 
 * 6228968
 * 
 * // Poprawne, bo spełniają kryteria
 * 000000000
 * 0120000
 */
include_once('../identificationNumberValidator.class.php');
include_once('regonValidator.class.php');

// Nowa funkcjonalność - czarna lista numerów
//regonValidator::addBlackList(array("000000000"));

$regon=new regonValidator('000001101');	
if($regon->isValid()) // jeśli jest prawdłowy
{
	print 'Podany numer REGON jest poprawny<br />';
}
else
{
	print 'Podany numer REGON jest nieprawidłowy<br />';
	
	//opcjonalnie, jeśli chcemy bardziej szczegółowe informacje
	if($regon->hasErrors())
	{
		foreach($regon->getErrors() as $error)
		{
			print $error['identifier'].', '.$error['code'].', '.$error['message'].'<br />';
		}
		
		print 'Kod błędu: '.$regon->getLastErrorCode();
	}
}

if($regon->isValid14())
{
	print 'Podany numer REGON jest prawidłowym numerem REGON14<br />';
}
?>