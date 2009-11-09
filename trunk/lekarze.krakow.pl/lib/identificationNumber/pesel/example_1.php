<?php
/**
 * Najprostsze użycie klasy
 */
include_once('../identificationNumberValidator.class.php');
include_once('peselValidator.class.php');

// Nowa funkcjonalność - czarna lista numerów
//peselValidator::addBlackList(array("00000000000"));

$pesel=new peselValidator('00000000000');

if($pesel->isValid()) // jeśli jest prawdłowy
{
	print 'Podany numer PESEL jest poprawny<br />';
}
else
{
	print 'Podany numer PESEL jest nieprawidłowy<br />';
	//opcjonalnie, jeśli chcemy bardziej szczegółowe informacje
	if($pesel->hasErrors())
	{
		foreach($pesel->getErrors() as $error)
		{
			//print$error.'<br />';
			print $error['identifier'].', '.$error['code'].', '.$error['message'].'<br />';
		}
	}
}
?>