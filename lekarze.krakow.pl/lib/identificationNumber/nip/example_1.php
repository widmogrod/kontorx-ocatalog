<?php
/**
 * Najprostsze użycie klasy
 */
include_once('../identificationNumberValidator.class.php');
include_once('nipValidator.class.php');

// Nowa funkcjonalność - czarna lista numerów
nipValidator::addBlackList(array("598-270-88-43"));

$nip=new nipValidator('PL599-270-87-03');	
//$nip=new nipValidator('123-456-32-18');
if($nip->isValid()) // jeśli jest prawdłowy
{
	print 'Podany numer NIP jest poprawny<br />';
}
else
{
	print 'Podany numer NIP jest nieprawidłowy<br />';
	//opcjonalnie, jeśli chcemy bardziej szczegółowe informacje
	if($nip->hasErrors())
	{
		foreach($nip->getErrors() as $error)
		{
			//print$error.'<br />';
			print $error['identifier'].', '.$error['code'].', '.$error['message'].'<br />';
		}
	}
}

print $nip->getDepartmentName().'<br />';
print $nip->getCountryName();
?>