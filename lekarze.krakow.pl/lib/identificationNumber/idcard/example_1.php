<?php
/**
 * Walidacja numeru dowodu osobistego
 * Najprostsze użycie klasy
 */
include_once('../identificationNumberValidator.class.php');
include_once('idcardValidator.class.php');

// Nowa funkcjonalność - czarna lista numerów
//peselValidator::addBlackList(array("00000000000"));

$idcard=new idcardValidator('ABS123456');

if($idcard->isValid()) // jeśli jest prawdłowy
{
	print 'Podana seria i numer dowodu są poprawne<br />';
}
else
{
	print 'Podana seria i numer dowodu są nieprawidłowe<br />';
	//opcjonalnie, jeśli chcemy bardziej szczegółowe informacje
	if($idcard->hasErrors())
	{
		foreach($idcard->getErrors() as $error)
		{
			//print$error.'<br />';
			print $error['identifier'].', '.$error['code'].', '.$error['message'].'<br />';
		}
	}
}
?>